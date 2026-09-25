<?php
declare(strict_types=1);

namespace ClasseTechnique;

use DateTime;

/**
 * Classe Requete
 *
 * Centralise la lecture et la validation des paramètres transmis
 * par le navigateur.
 *
 * Cette classe constitue l'unique point d'accès aux données HTTP.
 *
 * Les classes applicatives ne doivent jamais utiliser directement :
 *
 *      $_GET
 *      $_POST
 *      php://input
 *
 * Elles doivent utiliser cette classe.
 *
 * Deux modes de transmission sont supportés : formulaire HTML classique et requête JSON.
 *
 * Les méthodes typées permettent de récupérer directement une valeur convertie :
 * Les méthodes Nullable acceptent explicitement la valeur null
 *
 * Règles générales :
 *
 *  paramètre absent → UserException
 *  valeur null → UserException sauf méthode Nullable
 *  chaîne vide "" → UserException
 *  valeur incorrecte → UserException
 *  valeur valide → conversion et retour
 *
 * Une méthode Nullable accepte donc :
 *
 *  null → null
 *  valeur valide → valeur convertie
 *
 * Une chaîne vide n'est jamais considérée comme une valeur valide.
 *
 * @author Guy Verghote
 * @version 2026.3
 * @date    26/08/2026
 */
final class Requete
{
    /**
     * Cache des données JSON reçues.
     *
     * Le flux php://input ne pouvant être lu qu'une seule fois,
     * son contenu est mémorisé après décodage.
     */
    private static ?array $jsonData = null;

    /**
     * Empêche l'instanciation.
     */
    private function __construct()
    {
    }

    /**
     * Retourne la méthode HTTP utilisée.
     *
     * @return string
     */
    public static function methode(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? '');
    }

    /**
     * Vérifie la méthode HTTP utilisée.
     *
     * @param string $methode
     *

     */
    public static function exigerMethode(string $methode): void
    {
        if (self::methode() !== strtoupper($methode)) {
            ReponseJson::envoyerLesErreurs(['global' => "La méthode HTTP $methode est obligatoire."]);
        }
    }

    /**
     * Vérifie une requête POST obligatoirement appelée en Ajax.
     *
     * @throws UserException
     */
    public static function exigerPost(): void
    {
        self::exigerAjax();
        self::exigerMethode('POST');
        Jeton::verifier();
    }


    /**
     * Vérifie une requête POST obligatoirement appelée en ak=jax mais sans jeton CSRF.
     *

     */
    public static function exigerPostSansJeton(): void
    {
        if (!self::estAjax()) {
            ReponseJson::envoyerLesErreurs([
                'global' => 'Cette ressource est accessible uniquement par AJAX.'
            ]);
        }

        self::exigerMethode('POST');
    }

    /**
     * Vérifie une requête GET.
     *

     */
    public static function exigerGet(): void
    {
        self::exigerMethode('GET');
    }

    /**
     * Indique si la requête provient d'un appel AJAX.
     *
     * @return bool
     */
    public static function estAjax(): bool
    {
        return
            (
                isset($_SERVER['HTTP_X_REQUESTED_WITH'])
                && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
            )
            ||
            (
                isset($_SERVER['HTTP_ACCEPT'])
                && str_contains(strtolower($_SERVER['HTTP_ACCEPT']), 'application/json')
            );
    }

    /**
     * Vérifie que la requête est AJAX.
     *

     */
    private static function exigerAjax(): void
    {
        if (!self::estAjax()) {
            ReponseJson::envoyerLesErreurs(['global' => 'Cette ressource est accessible uniquement par AJAX.']);
        }
    }

    // ==========================================================
    // Lecture interne des données HTTP
    // ==========================================================

    /**
     * Retourne les données POST.
     *
     * Supporte : formulaire HTML et JSON.
     *
     * Pour une requête JSON, les données sont décodées une seule fois
     * puis conservées dans un cache interne.
     *
     * @return array
     *
     * @throws UserException
     */
    private static function postData(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            if (self::$jsonData === null) {
                $json = file_get_contents('php://input');
                $decoded = json_decode($json, true);
                if (!is_array($decoded)) {
                    throw new UserException("Les données JSON POST ne sont pas valides.");
                }
                self::$jsonData = $decoded;
            }
            return self::$jsonData;
        }
        return $_POST;
    }

    /**
     * Recherche une valeur dans un tableau.
     *
     * array_key_exists() est volontairement utilisé afin de différencier :
     *
     *      ['nom' => null]
     *
     * et :
     *
     *      []
     *
     * @param array $source
     * @param string $nom
     *
     * @return mixed
     *
     * @throws UserException
     */
    private static function readValue(array $source, string $nom): mixed
    {
        if (!array_key_exists($nom, $source)) {
            throw new UserException("Le paramètre $nom est absent.");
        }
        return $source[$nom];
    }

    // ==========================================================
    // Accès brut
    // ==========================================================

    /**
     * Retourne une valeur GET sans conversion.
     *
     * @param string $nom
     *
     * @return mixed
     *
     * @throws UserException
     */
    public static function get(string $nom): mixed
    {
        return self::readValue($_GET, $nom);
    }

    /**
     * Retourne une valeur POST sans conversion.
     *
     * Fonctionne avec : formulaire HTML et JSON.
     *
     * @param string $nom
     *
     * @return mixed
     *
     * @throws UserException
     */
    public static function post(string $nom): mixed
    {
        return self::readValue(self::postData(), $nom);
    }

    // ==========================================================
    // Convertisseurs privés
    // ==========================================================

    /**
     * Convertit une valeur en chaîne de caractères non vide.
     *
     * Les valeurs null et chaîne vide sont refusées.
     *
     * @param mixed $valeur
     * @param string $nom
     *
     * @return string
     *
     * @throws UserException
     */
    private static function asString(mixed $valeur, string $nom): string
    {
        if ($valeur === null) {
            throw new UserException("Le paramètre $nom ne peut pas être null.");
        }
        if (!is_string($valeur)) {
            throw new UserException("Le paramètre $nom doit être une chaîne de caractères.");
        }
        if ($valeur === '') {
            throw new UserException("Le paramètre $nom ne peut pas être vide.");
        }
        return $valeur;
    }

    /**
     * Convertit une valeur en entier.
     *
     * Les chaînes représentant un entier sont acceptées.
     * Les valeurs null et chaîne vide sont refusées.
     *
     * @param mixed $valeur
     * @param string $nom
     *
     * @return int
     *
     * @throws UserException
     */
    private static function asInt(mixed $valeur, string $nom): int
    {
        if ($valeur === null || $valeur === '') {
            throw new UserException("Le paramètre $nom ne peut pas être vide.");
        }
        if (filter_var($valeur, FILTER_VALIDATE_INT) === false) {
            throw new UserException("Le paramètre $nom doit être un entier.");
        }
        return (int)$valeur;
    }

    /**
     * Convertit une valeur en nombre décimal.
     *
     * Les chaînes numériques sont acceptées.
     * Les valeurs null et chaîne vide sont refusées.
     *
     * @param mixed $valeur
     * @param string $nom
     *
     * @return float
     *
     * @throws UserException
     */
    private static function asFloat(mixed $valeur, string $nom): float
    {
        if ($valeur === null || $valeur === '') {
            throw new UserException("Le paramètre $nom ne peut pas être vide.");
        }
        if (filter_var($valeur, FILTER_VALIDATE_FLOAT) === false) {
            throw new UserException("Le paramètre $nom doit être un nombre.");
        }
        return (float)$valeur;
    }

    /**
     * Convertit une valeur en booléen.
     *
     * Valeurs acceptées : true, false, 1, 0, "true", "false"
     *
     * Les valeurs null et chaîne vide sont refusées.
     *
     * @param mixed $valeur
     * @param string $nom
     *
     * @return bool
     *
     * @throws UserException
     */
    private static function asBool(mixed $valeur, string $nom): bool
    {
        if ($valeur === null || $valeur === '') {
            throw new UserException("Le paramètre $nom ne peut pas être vide.");
        }
        $resultat = filter_var($valeur, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
        if ($resultat === null) {
            throw new UserException("Le paramètre $nom doit être un booléen.");
        }
        return $resultat;
    }

    /**
     * Vérifie qu'une valeur est un tableau.
     *
     * La valeur null et les autres types sont refusés.
     * Un tableau vide reste autorisé.
     *
     * @param mixed $valeur
     * @param string $nom
     *
     * @return array
     *
     * @throws UserException
     */
    private static function asArray(mixed $valeur, string $nom): array
    {
        if ($valeur === null) {
            throw new UserException("Le paramètre $nom ne peut pas être null.");
        }
        if (!is_array($valeur)) {
            throw new UserException("Le paramètre $nom doit être un tableau.");
        }
        return $valeur;
    }

    /**
     * Convertit une valeur en chaîne représentant une date SQL.
     *
     * Format attendu : AAAA-MM-JJ.
     *
     * @param mixed $valeur
     * @param string $nom
     *
     * @return string
     *
     * @throws UserException
     */
    private static function asDate(mixed $valeur, string $nom): string
    {
        $valeur = self::asString($valeur, $nom);
        $date = DateTime::createFromFormat('Y-m-d', $valeur);
        if ($date === false || $date->format('Y-m-d') !== $valeur) {
            throw new UserException(
                "Le paramètre $nom doit être une date au format AAAA-MM-JJ."
            );
        }
        return $valeur;
    }

    /**
     * Vérifie qu'une valeur contient une adresse électronique valide.
     *
     * @param mixed $valeur
     * @param string $nom
     *
     * @return string
     *
     * @throws UserException
     */
    private static function asEmail(mixed $valeur, string $nom): string
    {
        $valeur = self::asString($valeur, $nom);
        if (filter_var($valeur, FILTER_VALIDATE_EMAIL) === false) {
            throw new UserException(
                "Le paramètre $nom doit contenir une adresse électronique valide."
            );
        }
        return $valeur;
    }

    /**
     * Vérifie qu'une valeur contient une URL valide.
     *
     * @param mixed $valeur
     * @param string $nom
     *
     * @return string
     *
     * @throws UserException
     */
    private static function asUrl(mixed $valeur, string $nom): string
    {
        $valeur = self::asString($valeur, $nom);
        if (filter_var($valeur, FILTER_VALIDATE_URL) === false) {
            throw new UserException(
                "Le paramètre $nom doit contenir une URL valide."
            );
        }
        return $valeur;
    }

    /**
     * Convertit une valeur simple en chaîne.
     *
     * Les valeurs acceptées sont : string non vide, int et float ;
     *
     * Les valeurs null, chaîne vide, booléens, tableaux et objets
     * sont refusés.
     *
     * Cette méthode est particulièrement adaptée aux clés primaires
     * dont le type peut varier selon les tables.
     *
     * @param mixed $valeur
     * @param string $nom
     *
     * @return string
     *
     * @throws UserException
     */
    private static function asScalar(mixed $valeur, string $nom): string
    {
        if ($valeur === null) {
            throw new UserException("Le paramètre $nom ne peut pas être null.");
        }
        if (is_string($valeur)) {
            if ($valeur === '') {
                throw new UserException("Le paramètre $nom ne peut pas être vide.");
            }
            return $valeur;
        }
        if (is_int($valeur) || is_float($valeur)) {
            return (string)$valeur;
        }
        throw new UserException("Le paramètre $nom doit être une valeur simple.");
    }

    // ==========================================================
    // Convertisseurs Nullable
    // ==========================================================

    /**
     * Convertit une valeur en chaîne ou accepte null.
     *
     * null est converti en null.
     * Une chaîne vide reste interdite.
     *
     * @param mixed $valeur
     * @param string $nom
     *
     * @return string|null
     *
     * @throws UserException
     */
    private static function asNullableString(mixed $valeur, string $nom): ?string
    {
        if ($valeur === null) {
            return null;
        }

        return self::asString($valeur, $nom);
    }

    /**
     * Convertit une valeur en entier ou accepte null.
     *
     * @param mixed $valeur
     * @param string $nom
     *
     * @return int|null
     *
     * @throws UserException
     */
    private static function asNullableInt(mixed $valeur, string $nom): ?int
    {
        if ($valeur === null) {
            return null;
        }
        return self::asInt($valeur, $nom);
    }

    /**
     * Convertit une valeur en nombre ou accepte null.
     *
     * @param mixed $valeur
     * @param string $nom
     *
     * @return float|null
     *
     * @throws UserException
     */
    private static function asNullableFloat(mixed $valeur, string $nom): ?float
    {
        if ($valeur === null) {
            return null;
        }
        return self::asFloat($valeur, $nom);
    }

    /**
     * Convertit une valeur en booléen ou accepte null.
     *
     * @param mixed $valeur
     * @param string $nom
     *
     * @return bool|null
     *
     * @throws UserException
     */
    private static function asNullableBool(mixed $valeur, string $nom): ?bool
    {
        if ($valeur === null) {
            return null;
        }
        return self::asBool($valeur, $nom);
    }

    /**
     * Convertit une valeur en date SQL ou accepte null.
     *
     * @param mixed $valeur
     * @param string $nom
     *
     * @return string|null
     *
     * @throws UserException
     */
    private static function asNullableDate(mixed $valeur, string $nom): ?string
    {
        if ($valeur === null) {
            return null;
        }
        return self::asDate($valeur, $nom);
    }

    // ==========================================================
    // Méthodes POST typées
    // ==========================================================

    /**
     * Retourne une chaîne POST non vide.
     *
     * @throws UserException
     */
    public static function postString(string $nom): string
    {
        return self::asString(self::post($nom), $nom);
    }

    /**
     * Retourne un entier POST.
     *
     * @throws UserException
     */
    public static function postInt(string $nom): int
    {
        return self::asInt(self::post($nom), $nom);
    }

    /**
     * Retourne un nombre décimal POST.
     *
     * @throws UserException
     */
    public static function postFloat(string $nom): float
    {
        return self::asFloat(self::post($nom), $nom);
    }

    /**
     * Retourne un booléen POST.
     *
     * @throws UserException
     */
    public static function postBool(string $nom): bool
    {
        return self::asBool(self::post($nom), $nom);
    }

    /**
     * Retourne un tableau POST.
     *
     * @throws UserException
     */
    public static function postArray(string $nom): array
    {
        return self::asArray(self::post($nom), $nom);
    }

    /**
     * Retourne une date POST valide.
     *
     * @throws UserException
     */
    public static function postDate(string $nom): string
    {
        return self::asDate(self::post($nom), $nom);
    }

    /**
     * Retourne une adresse électronique POST valide.
     *
     * @throws UserException
     */
    public static function postEmail(string $nom): string
    {
        return self::asEmail(self::post($nom), $nom);
    }

    /**
     * Retourne une URL POST valide.
     *
     * @throws UserException
     */
    public static function postUrl(string $nom): string
    {
        return self::asUrl(self::post($nom), $nom);
    }

    /**
     * Retourne une valeur scalaire POST.
     *
     * Cette méthode est particulièrement adaptée aux clés primaires.
     *
     * @throws UserException
     */
    public static function postScalar(string $nom): string
    {
        return self::asScalar(self::post($nom), $nom);
    }

    /**
     * Retourne une chaîne POST acceptant null.
     *
     * @param string $nom
     * @return string|null
     * @throws UserException
     */
    public static function postNullableString(string $nom): ?string
    {
        return self::asNullableString(self::post($nom), $nom);
    }

    /**
     * Retourne un entier POST acceptant null.
     *
     * @param string $nom
     * @return int|null
     * @throws UserException
     */
    public static function postNullableInt(string $nom): ?int
    {
        return self::asNullableInt(self::post($nom), $nom);
    }

    /**
     * Retourne un nombre POST acceptant null.
     *
     * @param string $nom
     * @return float|null
     * @throws UserException
     */
    public static function postNullableFloat(string $nom): ?float
    {
        return self::asNullableFloat(self::post($nom), $nom);
    }

    /**
     * Retourne un booléen POST acceptant null.
     *
     * @param string $nom
     * @return bool|null
     * @throws UserException
     */
    public static function postNullableBool(string $nom): ?bool
    {
        return self::asNullableBool(self::post($nom), $nom);
    }

    /**
     * Retourne une date POST acceptant null.
     *
     * @param string $nom
     * @return string|null
     * @throws UserException
     */
    public static function postNullableDate(string $nom): ?string
    {
        return self::asNullableDate(self::post($nom), $nom);
    }

    // ==========================================================
    // Méthodes GET typées
    // ==========================================================

    /**
     * Retourne une chaîne GET non vide.
     *
     * @throws UserException
     */
    public static function getString(string $nom): string
    {
        return self::asString(self::get($nom), $nom);
    }

    /**
     * Retourne un entier GET.
     *
     * @throws UserException
     */
    public static function getInt(string $nom): int
    {
        return self::asInt(self::get($nom), $nom);
    }

    /**
     * Retourne un nombre décimal GET.
     *
     * @throws UserException
     */
    public static function getFloat(string $nom): float
    {
        return self::asFloat(self::get($nom), $nom);
    }

    /**
     * Retourne un booléen GET.
     *
     * @throws UserException
     */
    public static function getBool(string $nom): bool
    {
        return self::asBool(self::get($nom), $nom);
    }

    /**
     * Retourne un tableau GET.
     *
     * @throws UserException
     */
    public static function getArray(string $nom): array
    {
        return self::asArray(self::get($nom), $nom);
    }

    /**
     * Retourne une date GET valide.
     *
     * @throws UserException
     */
    public static function getDate(string $nom): string
    {
        return self::asDate(self::get($nom), $nom);
    }

    /**
     * Retourne une adresse électronique GET valide.
     *
     * @throws UserException
     */
    public static function getEmail(string $nom): string
    {
        return self::asEmail(self::get($nom), $nom);
    }

    /**
     * Retourne une URL GET valide.
     *
     * @throws UserException
     */
    public static function getUrl(string $nom): string
    {
        return self::asUrl(self::get($nom), $nom);
    }

    /**
     * Retourne une valeur scalaire GET.
     *
     * @throws UserException
     */
    public static function getScalar(string $nom): string
    {
        return self::asScalar(self::get($nom), $nom);
    }

    /**
     * Retourne une chaîne GET acceptant null.
     *
     * @param string $nom
     * @return string|null
     * @throws UserException
     */
    public static function getNullableString(string $nom): ?string
    {
        return self::asNullableString(self::get($nom), $nom);
    }

    /**
     * Retourne un entier GET acceptant null.
     *
     * @param string $nom
     * @return int|null
     * @throws UserException
     */
    public static function getNullableInt(string $nom): ?int
    {
        return self::asNullableInt(self::get($nom), $nom);
    }

    /**
     * Retourne un nombre GET acceptant null.
     *
     * @param string $nom
     * @return float|null
     * @throws UserException
     */
    public static function getNullableFloat(string $nom): ?float
    {
        return self::asNullableFloat(self::get($nom), $nom);
    }

    /**
     * Retourne un booléen GET acceptant null.
     *
     * @param string $nom
     * @return bool|null
     * @throws UserException
     */
    public static function getNullableBool(string $nom): ?bool
    {
        return self::asNullableBool(self::get($nom), $nom);
    }

    /**
     * Retourne une date GET acceptant null.
     *
     * @param string $nom
     * @return string|null
     * @throws UserException
     */
    public static function getNullableDate(string $nom): ?string
    {
        return self::asNullableDate(self::get($nom), $nom);
    }

    // ==========================================================
    // Traitements de la variable $_FILES
    // ==========================================================

    /**
     * Retourne les informations d'un fichier téléversé.
     *
     * @param string $nom Nom du champ de type file.
     *
     * @return array
     *
     * @throws UserException Si le fichier est absent ou invalide.
     */
    public static function getFile(string $nom): array
    {
        if (!array_key_exists($nom, $_FILES)) {
            throw new UserException("Le champ fichier '$nom' est absent de la requête.");
        }

        if (!is_array($_FILES[$nom])) {
            throw new UserException("Le fichier '$nom' est invalide.");
        }

        return $_FILES[$nom];
    }

    // ==========================================================
    // Utilitaires
    // ==========================================================

    /**
     * Vérifie la présence du champ dans $_FILES.
     *
     * @param string $nom
     *
     * @return bool
     */
    public static function existeFichier(string $nom): bool
    {
        return array_key_exists($nom, $_FILES);
    }

    /**
     * Vérifie que $_FILES contient bien un fichier.
     *
     * @param string $nom
     *
     * @return bool
     */
    public static function fichierEnvoye(string $nom): bool
    {
        if (!array_key_exists($nom, $_FILES)) {
            return false;
        }

        return $_FILES[$nom]['error'] !== UPLOAD_ERR_NO_FILE;
    }
}
