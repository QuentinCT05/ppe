<?php
declare(strict_types=1);

namespace ClasseTechnique;

use PDO;
use PDOStatement;

/**
 * Centralise l'exécution des requêtes de consultation (SELECT).
 *
 * Cette classe évite de répéter dans les DAO le code de préparation,
 * d'exécution et de récupération des résultats.
 *
 * Les méthodes retournent :
 *  getRows() : un tableau de lignes (éventuellement vide)
 *  getRow() : une ligne ou null si aucune ligne
 *  getValue() : la valeur de la première colonne de la première ligne,
 *                ou false si aucune ligne (comportement natif de PDO).
 *
 * La connexion PDO est supposée configurée avec : PDO::ATTR_ERRMODE = ERRMODE_EXCEPTION
 *
 * @author  Guy Verghote
 * @version 2026.2
 * @date    12/08/2026
 */
class Select
{
    /**
     * Connexion à la base de données.
     */
    private PDO $db;

    /**
     * Initialise la connexion à la base de données.
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Exécute une requête retournant plusieurs lignes.
     *
     * Chaque ligne est renvoyée sous forme d'un tableau associatif
     * (nom de colonne → valeur).
     *
     * @param string $sql Requête SQL.
     * @param array $lesParametres Paramètres de la requête.
     * @return array Tableau des lignes trouvées (éventuellement vide).
     */
    public function getRows(string $sql, array $lesParametres = []): array
    {
        $cmd = $this->executer($sql, $lesParametres);

        try {
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        } finally {
            $cmd->closeCursor();
        }
    }

    /**
     * Exécute une requête retournant une seule ligne.
     *
     * @param string $sql Requête SQL.
     * @param array $lesParametres Paramètres de la requête.
     * @return array|null Tableau associatif ou null si aucune ligne.
     */
    public function getRow(string $sql, array $lesParametres = []): ?array
    {
        $cmd = $this->executer($sql, $lesParametres);

        try {
            $ligne = $cmd->fetch(PDO::FETCH_ASSOC);
            return $ligne === false ? null : $ligne;
        } finally {
            $cmd->closeCursor();
        }
    }

    /**
     * Exécute une requête retournant une seule valeur.
     *
     * Retourne la première colonne de la première ligne.
     * Le comportement est celui de PDO :
     *  false : aucune ligne trouvée
     *  sinon : valeur de la colonne (int, float, string, bool, null...)
     *
     * @param string $sql Requête SQL.
     * @param array $lesParametres Paramètres de la requête.
     * @return mixed
     */
    public function getValue(string $sql, array $lesParametres = []): mixed
    {
        $cmd = $this->executer($sql, $lesParametres);

        try {
            return $cmd->fetchColumn();
        } finally {
            $cmd->closeCursor();
        }
    }

    /**
     * Prépare et exécute une requête SQL.
     *
     * Si aucun paramètre n'est fourni, la requête est exécutée
     * directement avec query(). Sinon, elle est préparée puis exécutée.
     *
     * @param string $sql Requête SQL.
     * @param array $lesParametres Paramètres de la requête.
     * @return PDOStatement Requête exécutée.
     */
    private function executer(string $sql, array $lesParametres = []): PDOStatement
    {
        if ($lesParametres === []) {
            return $this->db->query($sql);
        }

        $cmd = $this->db->prepare($sql);
        $cmd->execute($lesParametres);

        return $cmd;
    }
}