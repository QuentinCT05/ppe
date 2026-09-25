<?php
declare(strict_types=1);

namespace ClasseMetier;

use ClasseTechnique\ColumnDate;
use ClasseTechnique\ColumnText;
use ClasseTechnique\ColumnTextarea;
use ClasseTechnique\Select;
use ClasseTechnique\Table;

/**
 * Gestion des épreuves.
 *
 * Cette classe représente la table epreuve.
 *
 * Responsabilités :
 * - définition des colonnes et de leurs règles de validation ;
 * - gestion des opérations CRUD via la classe Table ;
 * - consultation des épreuves.
 *
 * @author Guy Verghote
 * @version 2026.1
 */
class Epreuve extends Table
{
    /**
     * Configuration de la table.
     */
    protected function configure(): void
    {
        $this->table = 'epreuve';
        $this->primaryKey = 'saison';

        /**
         * Déclaration des colonnes.
         */
        // ==========================================================
        // Saison
        // ==========================================================

        $col = new ColumnText(
            required: true,
            insertable: true,
            updatable: false
        );

        $this->addColumn('saison', $col);

        // ==========================================================
        // Date de l'épreuve
        // ==========================================================

        $col = new ColumnDate(
            required: true,
            insertable: true,
            updatable: true,
            min: date('Y-m-d'),
            max: date('Y-m-d', strtotime('+1 year +3 month'))
        );

        $this->addColumn('date', $col);

        // ==========================================================
        // Description
        // ==========================================================

        $col = new ColumnTextarea(
            required: false,
            insertable: true,
            updatable: true
        );

        $this->addColumn('description', $col);


    }


    // -----------------------------------------------------------------------
    // Méthodes statiques de consultation
    // -----------------------------------------------------------------------

    /**
     * Retourne toutes les épreuves.
     */
    public static function getAll(): array
    {
        $sql = <<<SQL
            select saison, date, description
            from epreuve;
SQL;
        $select = new Select();
        return $select->getRows($sql);
    }

    /**
     * Retourne l'épreuve à venir
     * @return array|false
     */
    public static function getLesProchainesEpreuves()
    {
        $sql = <<<SQL
            Select date, description, saison
            From epreuve
            where date >= curdate() 
            order by date 
SQL;
        $select = new Select();
        return $select->getRows($sql);
    }

    public static function getProchaineEpreuve(): array|false
    {
        $sql = <<<SQL
            Select saison, date, description
            From epreuve
            where date >= curdate()
            order by date
            limit 1
SQL;
        $select = new Select();
        return $select->getRow($sql);
    }
}
