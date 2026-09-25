<?php
declare(strict_types=1);

namespace ClasseMetier;

use ClasseTechnique\UserException;

/**
 * Classe statique de gestion des accès applicatifs en fonction du rôle (membre / administrateur)
 * et du contexte d'exécution (répertoire, appel AJAX, etc.).
 *
 * Date : 26/07/2026
 * Auteur : Guy. Verghote
 */
class ControleAcces
{
    /**
     * Vérifie l'accès au répertoire courant selon le rôle
     * Appel à faire dans le script de contrôle
     */
    public static function verifier(): void
    {
        $path = dirname($_SERVER['PHP_SELF']);
        $segments = explode('/', trim($path, '/'));
        $repertoire = $segments[0] ?? '';
        $dernierChemin = end($segments);

        if ($repertoire === 'administration') {
            self::verifierAccesAdministration();
        } elseif ($repertoire === 'membre') {
            self::verifierAccesMembre();
        }

    }

    /**
     * Vérifie que l'utilisateur est connecté et est un administrateur autorisé
     */
    public static function verifierAccesAdministration(): void
    {
        if (!isset($_SESSION['membre'])) {
            // Redirection vers la page de connexion
            self::seConnecter();
            // throw new UserException("Vous devez être connecté pour accéder à cette fonctionnalité");
        }

        $idMembre = $_SESSION['membre']['id'];

        if (!Administrateur::estUnAdministrateur($idMembre)) {
            throw new UserException("Vous devez être administrateur pour accéder à cette fonctionnalité");
        }

        // Récupération du répertoire courant
        $repertoire = basename(dirname($_SERVER['PHP_SELF']));

        // Accès autorisé pour les répertoires fixes + ceux spécifiquement autorisés
        $repertoiresLibres = ['administration', 'ajax'];
        if (!in_array($repertoire, $repertoiresLibres) && !Administrateur::peutAdministrer($idMembre, $repertoire)) {
            throw new UserException("Vous n'avez pas les droits pour accéder au répertoire : $repertoire");
        }
    }

    /**
     * Vérifie que le membre est connecté (utilisé pour l’espace membre)
     */
    public static function verifierAccesMembre(): void
    {
        if (!isset($_SESSION['membre'])) {
            // Redirection vers la page de connexion
            self::seConnecter();
            // throw new UserException("Vous devez être connecté pour accéder à votre espace");
        }
    }

    /**
     * Redirige vers la page de connexion en mémorisant l'URL demandée dans la session
     */
    private static function seConnecter(): void
    {
        // Mémorisation de la page demandée
        $_SESSION['url'] = $_SERVER['REQUEST_URI'];
        // Redirection vers la page de connexion
        header("Location: /connexion");
        exit;
    }
}
