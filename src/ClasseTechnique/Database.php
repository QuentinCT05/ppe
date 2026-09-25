<?php

declare(strict_types=1);

namespace ClasseTechnique;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Classe Database : gestion centralisée de la connexion PDO.
 *
 * @author Guy Verghote
 * @version 2026.3
 * @date 12/08/2026
 */
class Database
{
    /**
     * Instance unique de la connexion PDO.
     */
    private static ?PDO $instance = null;

    /**
     * Retourne l'instance PDO unique.
     *
     * @return PDO
     *
     * @throws RuntimeException Si la configuration est invalide.
     * @throws PDOException Si la connexion à la base de données échoue.
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $lesParametres = self::getLesParametres();

            $host = $lesParametres['host'];
            $database = $lesParametres['database'];
            $user = $lesParametres['user'];
            $password = $lesParametres['password'];
            $port = $lesParametres['port'];

            $charset = 'utf8mb4';

            $chaine = "mysql:host=$host;dbname=$database;port=$port;charset=$charset";

            $db = new PDO($chaine, $user, $password);

            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $db->exec("SET sql_mode = 'STRICT_ALL_TABLES,ONLY_FULL_GROUP_BY'");

            self::$instance = $db;
        }

        return self::$instance;
    }

    /**
     * Lecture du fichier de configuration config/database.php.
     *
     * @return array<string, mixed>
     *
     * @throws RuntimeException Si la configuration est absente ou invalide.
     */
    public static function getLesParametres(): array
    {
        $lesParametres = Config::chargerPhp('database');

        // Vérification de la récupération d'un tableau.
        if (!is_array($lesParametres)) {
            throw new RuntimeException("La configuration database doit retourner un tableau.");
        }

        // Vérification des paramètres obligatoires.
        foreach (['host', 'database', 'user', 'password'] as $key) {
            if (!isset($lesParametres[$key])) {
                throw new RuntimeException("Clé manquante dans le fichier de configuration /config/database.php : $key");
            }
        }

        // Port MySQL par défaut.
        $lesParametres['port'] = $lesParametres['port'] ?? 3306;

        return $lesParametres;
    }
}