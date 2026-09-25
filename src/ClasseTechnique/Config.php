<?php
declare(strict_types=1);

namespace ClasseTechnique;

use Exception;

/**
 * Classe Config
 *
 * Gestion centralisée des fichiers de configuration.
 *
 * Deux formats sont supportés :
 *
 * - PHP :
 *      Le fichier doit retourner une valeur avec return.
 *
 *      Exemple :
 *          return ['host' => 'localhost'];
 *
 * - JSON :
 *      Le fichier contient une donnée structurée.
 *
 *      Exemple :
 *          {
 *              "projet": ["id", "nom"]
 *          }
 *
 * @Author : Guy Verghote
 * @Version 2026.3
 * @Date : 31/07/2026
 */
final class Config
{

    /**
     * Empêche l'instanciation.
     */
    private function __construct()
    {
    }


    /**
     * Vérifie le nom d'une configuration.
     *
     * @param string $nom
     *
     * @throws Exception
     */
    private static function verifierNom(string $nom): void
    {
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $nom)) {
            throw new Exception("Nom de configuration invalide : $nom");
        }
    }


    /**
     * Retourne le chemin d'un fichier de configuration.
     *
     * @param string $nom
     * @param string $extension
     *
     * @return string
     */
    private static function obtenirChemin(string $nom, string $extension): string
    {
        return DOSSIER_CONFIG . '/' . $nom . '.' . $extension;
    }


    /**
     * Charge une configuration PHP.
     *
     * Le fichier doit retourner une valeur.
     *
     * Exemple :
     *
     * config/database.php
     *
     * return [
     *     'host' => 'localhost'
     * ];
     *
     *
     * @param string $nom
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function chargerPhp(string $nom): mixed
    {
        self::verifierNom($nom);

        $fichier = self::obtenirChemin($nom, 'php');

        if (!is_file($fichier)) {
            throw new Exception("FileManager de configuration PHP introuvable : $nom");
        }
        $configuration = require $fichier;
        if ($configuration === null) {
            throw new Exception("Le fichier de configuration '$nom' ne retourne aucune donnée." );
        }
        return $configuration;
    }


    /**
     * Charge une configuration JSON.
     *
     * Le fichier doit contenir un objet JSON
     * correspondant à un tableau associatif PHP.
     *
     * @param string $nom
     *
     * @return array
     *
     * @throws Exception
     */
    public static function chargerJson(string $nom): array
    {
        self::verifierNom($nom);
        $fichier = self::obtenirChemin($nom, 'json');
        if (!is_file($fichier)) {
            throw new Exception("Fichier de configuration JSON introuvable : $nom" );
        }
        $contenu = file_get_contents($fichier);
        if ($contenu === false) {
            throw new Exception("Impossible de lire le fichier de configuration : $nom");
        }

        $configuration = json_decode($contenu, true);

        if (!is_array($configuration)) {
            throw new Exception("Le fichier de configuration JSON '$nom' est invalide.");
        }
        return $configuration;
    }

}