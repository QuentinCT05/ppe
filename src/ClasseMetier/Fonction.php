<?php
declare(strict_types=1);

namespace ClasseMetier;

use ClasseTechnique\ColumnText;
use ClasseTechnique\Select;
use ClasseTechnique\Table;

/**
 * Gestion de la table fonction.
 *
 * Clé primaire : repertoire.
 *
 * @author Guy Verghote
 * @version 2026.1
 */
class Fonction extends Table
{
    /**
     * Configuration de la table.
     */
    protected function configure(): void
    {
        $this->table = 'fonction';
        $this->primaryKey = 'repertoire';

        // ---------------------------------------------------------
        // repertoire
        // ---------------------------------------------------------
        $col = new ColumnText(
            required: true,
            insertable: true,
            updatable: true,
            minLength: 4,
            maxLength: 50,
            pattern: '^[a-zA-Z0-9]{4,50}$'
        );
        $this->addColumn('repertoire', $col);

        // ---------------------------------------------------------
        // nom
        // ---------------------------------------------------------
        $col = new ColumnText(
            required: true,
            insertable: true,
            updatable: true,
            minLength: 10,
            maxLength: 150,
            pattern: "^[A-Za-zÀ-ÖØ-öø-ÿ0-9](?:[A-Za-zÀ-ÖØ-öø-ÿ0-9' ]*[A-Za-zÀ-ÖØ-öø-ÿ0-9])?$"
        );
        $this->addColumn('nom', $col);
    }

    // ==========================================================
    // Méthodes de consultation
    // ==========================================================

    /**
     * Retourne la liste des fonctions.
     */
    public static function getAll(): array
    {
        $sql = <<<SQL
            SELECT repertoire,
                   nom
            FROM fonction
            ORDER BY repertoire;
SQL;

        $select = new Select();

        return $select->getRows($sql);
    }
}