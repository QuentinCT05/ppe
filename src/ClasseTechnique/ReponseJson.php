<?php

declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Classe ReponseJson
 *
 * Centralise la production des réponses JSON de l'application.
 *
 * La méthode envoyer() constitue le point d'entrée générique :
 *
 *     ReponseJson::envoyer($contenu, $codeHttp);
 *
 * Les méthodes spécialisées sont des façades permettant de simplifier
 * les cas d'utilisation courants :
 *
 *     envoyerMessage()      → message de succès
 *     envoyerLesDonnees()   → données
 *     envoyerCreation()     → création d'une ressource
 *     envoyerLesErreurs()   → erreurs de validation
 *     envoyerErreur()       → erreur générale
 *
 * Toutes les réponses terminent le script par exit.
 *
 * La classe fournit également une méthode permettant d'encoder
 * du JSON destiné à être intégré dans une page HTML.
 *
 * @author Guy Verghote
 * @version 2026.7
 * @date 26/08/2026
 */
final class ReponseJson
{
    /**
     * Options utilisées pour les réponses HTTP JSON.
     */
    private const int OPTIONS_HTTP =
        JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
        | JSON_THROW_ON_ERROR;

    /**
     * Options utilisées pour intégrer du JSON dans une page HTML.
     *
     * Les caractères pouvant être interprétés comme du HTML
     * sont convertis afin d'éviter toute interprétation
     * par le navigateur.
     */
    private const int OPTIONS_SCRIPT =
        JSON_HEX_TAG
        | JSON_HEX_AMP
        | JSON_HEX_APOS
        | JSON_HEX_QUOT
        | JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
        | JSON_THROW_ON_ERROR;

    /**
     * Constructeur privé.
     *
     * Cette classe est une classe utilitaire statique.
     */
    private function __construct()
    {
    }

    /**
     * Envoie une réponse HTTP au format JSON puis termine le script.
     *
     * Cette méthode constitue le mécanisme générique de la classe.
     *
     * @param mixed $contenu Contenu de la réponse.
     * @param int $codeHttp Code HTTP de la réponse.
     *
     * @return never
     */
    public static function envoyer(mixed $contenu = null, int $codeHttp = 200): never
    {
        http_response_code($codeHttp);
        header('Content-Type: application/json; charset=utf-8');
        echo self::encoder($contenu);
        exit;
    }

    /**
     * Envoie un message de confirmation.
     *
     * Code HTTP : 200 OK
     *
     * Exemple :
     *
     *     ReponseJson::envoyerMessage(
     *         'Opération réalisée avec succès.'
     *     );
     *
     * @param string $message
     *
     * @return never
     */
    public static function envoyerMessage(string $message = 'Opération réalisée avec succès.'): never
    {
        self::envoyer(['message' => $message]);
    }

    /**
     * Envoie des données.
     *
     * Code HTTP : 200 OK
     *
     * Exemple :
     *
     *     ReponseJson::envoyerLesDonnees([
     *         'nom' => 'Bernard',
     *         'prenom' => 'Jean'
     *     ]);
     *
     * @param array $lesDonnees
     *
     * @return never
     */
    public static function envoyerLesDonnees(array $lesDonnees = []): never
    {
        self::envoyer($lesDonnees);
    }

    /**
     * Envoie la réponse correspondant à la création
     * d'une ressource.
     *
     * Code HTTP : 201 Created
     *
     * @param int|string $id Identifiant de la ressource créée.
     *
     * @return never
     */
    public static function envoyerCreation(int|string $id): never
    {
        self::envoyer(['id' => $id], 201);
    }

    /**
     * Envoie une liste d'erreurs de validation.
     *
     * Code HTTP par défaut : 422 Unprocessable Content
     *
     * Exemple :
     *
     *     ReponseJson::envoyerLesErreurs([
     *         'password' => 'Mot de passe invalide.'
     *     ]);
     *
     * @param array $lesErreurs
     * @param int $codeHttp
     *
     * @return never
     */
    public static function envoyerLesErreurs(array $lesErreurs, int $codeHttp = 422): never
    {
        self::envoyer(['errors' => $lesErreurs], $codeHttp);
    }

    /**
     * Envoie une erreur générale.
     *
     * Code HTTP par défaut : 400 Bad Request
     *
     * @param string $message
     * @param int $codeHttp
     *
     * @return never
     */
    public static function envoyerErreur(string $message, int $codeHttp = 400): never
    {
        self::envoyer(['error' => $message], $codeHttp);
    }

    /**
     * Encode une valeur au format JSON.
     *
     * Tous les appels à json_encode() destinés aux réponses HTTP
     * passent par cette méthode.
     *
     * @param mixed $valeur
     *
     * @return string
     */
    private static function encoder(mixed $valeur): string
    {
        return json_encode($valeur, self::OPTIONS_HTTP);
    }

    /**
     * Encode une valeur au format JSON pour une utilisation
     * dans une page HTML.
     *
     * Cette méthode est notamment destinée :
     *
     * - aux balises <script type="application/json">
     * - aux attributs data-*
     *
     * Les caractères pouvant être interprétés comme du HTML
     * sont encodés afin de prévenir les attaques XSS.
     *
     * @param mixed $valeur
     *
     * @return string
     */
    public static function encoderPourHtml(mixed $valeur): string
    {
        return json_encode($valeur, self::OPTIONS_SCRIPT);
    }
}