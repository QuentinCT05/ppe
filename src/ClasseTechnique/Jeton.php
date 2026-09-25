<?php
declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Classe Jeton : gestion des jetons CSRF
 *
 * ============================================================================
 * OBJECTIF
 * ============================================================================
 *
 * Protège l'application contre les attaques CSRF
 * (Cross-Site Request Forgery).
 *
 * Une attaque CSRF consiste à faire exécuter une requête à un utilisateur
 * authentifié sans son consentement.
 *
 * Exemple :
 *
 * L'utilisateur est connecté à l'application ;
 * il visite un autre site malveillant ;
 * ce site tente d'envoyer une requête POST vers notre application ;
 * le navigateur transmet automatiquement les cookies de session ;
 * sans protection, l'action pourrait être exécutée.
 *
 * ============================================================================
 * PRINCIPE UTILISÉ
 * ============================================================================
 *
 * Pattern : Synchronizer Token Pattern.
 *
 * Le fonctionnement est le suivant :
 *
 * 1) Le serveur génère un jeton aléatoire.
 *
 * 2) Le jeton est stocké dans la session PHP.
 *
 * 3) Lorsqu'une page possède des actions AJAX nécessitant une protection,
 *    le jeton est injecté dans le HTML :
 *
 *       <meta name="csrf-token" content="">
 *
 * 4) Le JavaScript récupère cette valeur.
 *
 * 5) Chaque requête sensible transmet le jeton dans un header HTTP :
 *
 *       X-CSRF-Token: xxxxx
 *
 * 6) Le serveur compare le jeton reçu et le jeton enregistré en session
 *
 * ============================================================================
 * POURQUOI UN HEADER HTTP ?
 * ============================================================================
 *
 * Un formulaire HTML provenant d'un autre site peut envoyer : POST /suppression.php
 *
 * mais il ne peut pas ajouter librement un header personnalisé : X-CSRF-Token
 *
 * Le navigateur applique alors une restriction qui protège l'application.
 *
 *
 * ============================================================================
 * GESTION DU JETON
 * ============================================================================
 *
 * Le jeton n'a pas de durée de vie propre.
 *
 * Il est valable :
 *
 *       Pendant toute la durée de la session utilisateur ;
 *       jusqu'à la fermeture de session ;
 *       ou jusqu'à sa régénération volontaire.
 *
 * Avantage : Plusieurs onglets ouverts simultanément utilisent le même jeton.
 *
 * Exemple :
 *
 * - onglet A : modification d'une annonce ;
 * - onglet B : consultation d'une autre page ;
 *
 * Les deux pages restent compatibles.
 *
 * ============================================================================
 *
 * @author Guy Verghote
 * @version 2026.4
 * @date : 08/08/2026
 */
final class Jeton
{
    /**
     * Clé utilisée dans $_SESSION.
     *
     * Centralisée pour éviter les erreurs de frappe.
     */
    private const string SESSION_KEY = 'csrf_token';


    /**
     * Refuse une requête non conforme.
     *
     * Toutes les erreurs CSRF retournent HTTP 403.
     *
     * @param string $message Message d'erreur
     *
     * @throws UserException
     */
    private static function refuser(string $message): never
    {
        http_response_code(403);

        throw new UserException($message);
    }


    /**
     * Création ou récupération du jeton CSRF.
     *
     * Si un jeton existe déjà dans la session, il est réutilisé.
     *
     * Cela évite qu'une page ouverte dans un autre onglet
     * devienne invalide.
     *
     * @return string Jeton CSRF
     *
     * @throws \Exception Si la génération de bytes aléatoires échoue
     */
    public static function creer(): string
    {
        if (isset($_SESSION[self::SESSION_KEY]) && is_string($_SESSION[self::SESSION_KEY]) && $_SESSION[self::SESSION_KEY] !== '') {
            return $_SESSION[self::SESSION_KEY];
        }

        // random_bytes fournit une valeur aléatoire cryptographiquement sûre sur 32 octets
        $token = bin2hex(random_bytes(32));
        $_SESSION[self::SESSION_KEY] = $token;
        return $token;
    }

    /**
     * Vérification du jeton CSRF reçu.
     *
     * Contrôles réalisés :
     *
     * 1) Le jeton existe en session.
     * 2) Le navigateur a transmis un header.
     * 3) Les deux valeurs correspondent.
     *
     * @return void
     *
     * @throws UserException
     */
    public static function verifier(): void
    {
        // Vérification de l'existence du jeton serveur.
        if (!isset($_SESSION[self::SESSION_KEY]) || !is_string($_SESSION[self::SESSION_KEY])) {
            self::refuser("Les informations de sécurité ne sont plus disponibles. Veuillez recharger la page.");
        }

        // Lecture du header envoyé par JavaScript.
        $tokenRecu = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if ($tokenRecu === '') {
            self::refuser("Le jeton de sécurité est absent de la requête.");
        }

        // Comparaison sécurisée : hash_equals évite les attaques par analyse du temps d'exécution de la comparaison.
        if (!hash_equals($_SESSION[self::SESSION_KEY], $tokenRecu)) {
            self::refuser("La vérification de sécurité a échoué.");
        }
    }

    /**
     * Suppression du jeton CSRF.
     *
     * Utile lors d'une déconnexion par exemple.
     */
    public static function supprimer(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
    }
}