<?php
declare(strict_types=1);

namespace ClasseTechnique;

use PDOException;
use Throwable;

/**
 * Classe Erreur : gestion centralisée des erreurs applicatives et SQL.
 *
 * STRATÉGIE :
 * _ Toute exception est journalisée avec ses détails techniques complets
 * - Réponse utilisateur en deux cas seulement :
 *   • UserException → le message réel
 *   • Tout autre Throwable → message générique
 *
 * @author Guy Verghote
 * @Version 2026.5
 * @Date : 17/09/2026
 */
enum TypeReponse: string
{
    // Réponse HTML classique (redirection vers page d'erreur)
    case HTML = 'html';
    // Réponse JSON (API clients)
    case JSON = 'json';
}

class Erreur
{
    // Message générique affiché pour toute erreur non-applicative
    private const string MSG_SYSTEME = "Une erreur technique est survenue, veuillez réessayer ultérieurement.";

    // Mapping des codes d'erreur MySQL/MariaDB connus vers un message lisible
    // Ces messages sont présentés comme UserException après journalisation
    private static array $lesCodesSql = [
        1062 => "Enregistrement déjà existant.",
        1048 => "Une information obligatoire est manquante.",
        1406 => "Une information est trop longue.",
        1366 => "Format de donnée invalide.",
        1452 => "Donnée invalide (référence inexistante).",
        1451 => "Suppression impossible : donnée utilisée.",
        3819 => "Une valeur saisie ne respecte pas une règle de validation.",
        4025 => "Une valeur saisie ne respecte pas une règle de validation.",
    ];

    // Tableau qui contiendra après chargement du fichier de configuration config/contrainte.php lié à l'application
    private static array $lesContraintes = [];

    /**
     * Définit les contraintes spécifiques à l'application.
     *
     * Le tableau attendu doit être associatif : ['nom_contrainte' => 'message utilisateur'].
     *
     * En cas de configuration incorrecte, l'erreur est journalisée
     * et aucune contrainte spécifique n'est chargée.
     *
     * @param array $lesContraintes
     */
    public static function definirLesContraintes(array $lesContraintes): void
    {
        // Vérifie que le tableau possède bien des clés associatives
        if (array_is_list($lesContraintes)) {
            Journal::enregistrer("Le fichier de configuration des contraintes doit contenir un tableau associatif.", 'erreur');
            self::$lesContraintes = [];
            return;
        }

        // Vérifie le contenu de chaque contrainte
        foreach ($lesContraintes as $nom => $message) {
            if ($nom === '' || !is_string($nom)) {
                Journal::enregistrer("Nom de contrainte invalide dans la configuration.", 'erreur');
                self::$lesContraintes = [];
                return;
            }

            if (!is_string($message) || $message === '') {
                Journal::enregistrer("Message invalide pour la contrainte '$nom'.", 'erreur');

                self::$lesContraintes = [];
                return;
            }
        }

        self::$lesContraintes = $lesContraintes;
    }

    /**
     * Installe le handler global ; c'est le seul point d'entrée public.
     */
    public static function installerGestionnaire(): void
    {
        set_exception_handler(static function (Throwable $e): void {
            self::traiterReponse($e);
        });
    }

    /**
     * Traitement interne de toutes les exceptions.
     *
     * 1. Journalisation systématique des détails techniques
     * 2. Construction de la réponse utilisateur :
     *    UserException : message réel
     *    PDOException : message lisible si code connu, sinon générique
     *    Throwable: message générique
     */
    private static function traiterReponse(Throwable $e): void
    {
        if ($e instanceof UserException) {
            /*
             * Une erreur métier est volontairement destinée
             * à l'utilisateur.
             *
             * Elle n'est pas journalisée par défaut.
             */
            $message = $e->getMessage();
            $codeHttp = $e->getCodeHttp();
        } elseif ($e instanceof PDOException) {
            [$message, $codeHttp, $doitJournaliser] = self::resoudreMessageSQL($e);

            if ($doitJournaliser) {
                self::journaliser($e);
            }
        } else {
            /*
             * Toute autre exception est considérée
             * comme une erreur technique imprévue.
             */
            self::journaliser($e);

            $message = self::MSG_SYSTEME;
            $codeHttp = 500;
        }

        self::rendreReponse($message, $codeHttp);
    }

    /**
     * Journalisation systématique avec tous les détails techniques de l'exception.
     */
    private static function journaliser(Throwable $e): void
    {
        $detail = sprintf('[%s] %s | code : %s | fichier : %s | ligne : %d', get_class($e), $e->getMessage(), $e->getCode(), $e->getFile(), $e->getLine());
        Journal::enregistrer($detail, 'erreur');
    }

    private static function resoudreMessageSQL(PDOException $e): array
    {
        $errorInfo = $e->errorInfo ?? [];
        [$sqlState, $codeErreur, $message] = array_pad($errorInfo, 3, null);

        // 1) SIGNAL SQL métier explicite
        if ($sqlState === '45000') {
            return [(string)$message, 400, false];
        }

        // 2) Contrainte applicative configurée (nom explicite de contrainte)
        if (is_string($message)) {
            foreach (self::$lesContraintes as $nom => $libelle) {
                if (str_contains($message, $nom)) {
                    return [$libelle, 400, false];
                }
            }
        }

        // 2bis) Cas particulier MySQL doublon PK => key 'PRIMARY'
        // Permet un message spécifique par table via des clés de config :
        //  - primary@coureur
        //  - primary@categorie
        //  - primary (fallback)
        if ((int)$codeErreur === 1062 && is_string($message)) {
            $table = self::extraireTableDepuisMessageDoublon($message);

            if ($table !== null) {
                $cleTable = 'primary@' . strtolower($table);
                if (isset(self::$lesContraintes[$cleTable])) {
                    return [self::$lesContraintes[$cleTable], 400, false];
                }
            }

            $messagePrimaire = self::resoudreMessagePrimaryParContexte();
            if ($messagePrimaire !== null) {
                return [$messagePrimaire, 400, false];
            }

            if (isset(self::$lesContraintes['primary'])) {
                return [self::$lesContraintes['primary'], 400, false];
            }
        }

        // 3) CHECK générique
        if (self::estErreurCheckConstraint($codeErreur, $message)) {
            return ["Une valeur saisie ne respecte pas une règle de validation.", 400, false];
        }

        // 4) Code SQL connu
        if (isset(self::$lesCodesSql[(int)$codeErreur])) {
            return [self::$lesCodesSql[(int)$codeErreur], 400, false];
        }

        // 5) Cas non identifié
        return [self::MSG_SYSTEME, 500, true];
    }

    /**
     * Résout un message PRIMARY spécifique en s'appuyant sur l'URL courante.
     * Exemple : /coureur/maj/ajax/ajouter.php => primary@coureur
     */
    private static function resoudreMessagePrimaryParContexte(): ?string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (!is_string($uri) || $uri === '') {
            return null;
        }

        if (preg_match('#/([a-z0-9_-]+)/#i', $uri, $m) === 1 && isset($m[1])) {
            $cleTable = 'primary@' . strtolower($m[1]);
            if (isset(self::$lesContraintes[$cleTable])) {
                return self::$lesContraintes[$cleTable];
            }
        }

        return null;
    }

    /**
     * Extrait le nom de table d'un message MySQL 1062.
     * Exemples gérés :
     *  - "Duplicate entry 'x' for key 'PRIMARY'" (pas de table)
     *  - "Duplicate entry 'x' for key 'gestion.coureur.PRIMARY'"
     */
    private static function extraireTableDepuisMessageDoublon(string $message): ?string
    {
        if (
            preg_match("/for key '([^']+)'/i", $message, $m) !== 1
            || !isset($m[1])
        ) {
            return null;
        }

        $key = $m[1]; // ex: PRIMARY ou gestion.coureur.PRIMARY
        $segments = explode('.', $key);

        if (count($segments) >= 3) {
            // db.table.index
            return $segments[count($segments) - 2];
        }

        return null;
    }

    /**
     * Détecte les violations de contraintes CHECK (MySQL code 3819, MariaDB code 4025).
     * Fallback sur le message texte si le code n'est pas exploitable.
     */
    private static function estErreurCheckConstraint(mixed $codeErreur, mixed $message): bool
    {
        if (in_array((int)$codeErreur, [3819, 4025], true)) {
            return true;
        }

        if (!is_string($message) || $message === '') {
            return false;
        }

        return (bool)preg_match('/check constraint|constraint .* is violated|violated/i', $message);
    }

    /**
     * Envoie la réponse dans le format attendu (JSON ou redirection HTML).
     */
    private static function rendreReponse(string $message, int $codeHttp): void
    {
        // Si une partie de la page a déjà été générée, elle est encore dans le buffer grâce à ob_start().
        // On la supprime afin que la réponse d'erreur soit propre.
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        if (self::getTypeReponse() === TypeReponse::JSON) {
            ReponseJson::envoyerErreur($message, $codeHttp);
        } else {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['erreur'] = $message;
            header('Location: /erreur');
            exit;
        }
    }

    /**
     * Détermine le type de réponse attendu : JSON si requête AJAX ou Accept contient application/json.
     */
    private static function getTypeReponse(): TypeReponse
    {
        if (
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower((string)$_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) {
            return TypeReponse::JSON;
        }

        if (
            isset($_SERVER['HTTP_ACCEPT']) &&
            str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')
        ) {
            return TypeReponse::JSON;
        }

        return TypeReponse::HTML;
    }

    /**
     * Retourne un libellé lisible pour un code HTTP donné.
     */
    public static function getErreurHttp(int|string|null $codeHttp): string
    {
        if ($codeHttp === null) {
            return "Erreur HTTP : code inconnu";
        }

        $libelles = [
            400 => "Requête incorrecte",
            401 => "Erreur d'authentification",
            403 => "Demande interdite",
            404 => "Page non trouvée",
            405 => "Méthode non autorisée",
            408 => "Temps d'attente d'une requête dépassé",
            500 => "Erreur interne du serveur",
            502 => "Mauvaise passerelle",
            503 => "Service indisponible",
            504 => "Temps d'attente de la passerelle dépassé"
        ];

        return $libelles[$codeHttp] ?? "Erreur HTTP : " . $codeHttp;
    }
}