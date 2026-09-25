<?php
declare(strict_types=1);

namespace ClasseMetier;

use ClasseTechnique\ColumnEmail;
use ClasseTechnique\ColumnInt;
use ClasseTechnique\ColumnText;
use ClasseTechnique\Database;
use ClasseTechnique\Select;
use ClasseTechnique\Table;
use ClasseTechnique\TextCase;
use ClasseTechnique\UserException;

/**
 * Classe métier représentant un membre.
 *
 */
class Membre extends Table
{

    /**
     * Configuration de la table.
     */
    protected function configure(): void
    {
        $this->table = 'membre';
        $this->primaryKey = 'id';


        // ------------------------------------------------------
        // nom
        // ------------------------------------------------------

        $column = new ColumnText(
            required: true,
            insertable: true,
            updatable: false,
            casse: TextCase::Upper,
            supprimerAccent: true,
            supprimerEspaceSuperflu: true,
            pattern: "^[A-Z]( ?[A-Z]+)*$",
            maxLength: 30
        );

        $this->addColumn('nom', $column);


        // ------------------------------------------------------
        // prenom
        // ------------------------------------------------------

        $column = new ColumnText(
            required: true,
            insertable: true,
            updatable: false,
            casse: TextCase::Upper,
            supprimerAccent: true,
            supprimerEspaceSuperflu: true,
            pattern: "^[A-Z]( ?[A-Z]+)*$",
            maxLength: 30
        );
        $this->addColumn('prenom', $column);

        // ------------------------------------------------------
        // email
        // ------------------------------------------------------

        $column = new ColumnEmail(
            required: true,
            insertable: true,
            updatable: false,
            maxLength: 100
        );
        $this->addColumn('email', $column);

        // ------------------------------------------------------
        // photo
        // ------------------------------------------------------

        $column = new ColumnText(
            required: false,
            insertable: false,
            updatable: true
        );
        $this->addColumn('photo', $column);


        // ------------------------------------------------------
        // telephone
        // ------------------------------------------------------

        $column = new ColumnText(
            required: false,
            insertable: true,
            updatable: true
        );
        $this->addColumn('telephone', $column);

        // ------------------------------------------------------
        // autMail
        // ------------------------------------------------------

        $column = new ColumnInt(
            required: false,
            insertable: false,
            updatable: true,
            min: 0,
            max: 1
        );
        $this->addColumn('autMail', $column);

        /*
         * login et password ne sont volontairement pas déclarés ici :
         * ils sont gérés par les traitements spécifiques de connexion.
         */
    }


    // ==========================================================
    // Consultation
    // ==========================================================

    /**
     * Liste des membres pour administration.
     */
    public static function getAll(): array
    {
        $sql = <<<SQL
            select id,
                   concat(nom, ' ', prenom) as nomPrenom,
                   nom, prenom, email,
                   ifnull(photo, 'Non renseignée') as photo,
                   login,
                   if(autMail, 'Oui', 'Non') as afficherMail,
                   ifnull(telephone, 'Non renseigné') as telephone,
                   saison
            from membre
            order by nom, prenom;
SQL;
        $select = new Select();
        return $select->getRows($sql);
    }

    /**
     * Récupère les membres pour affichage dans l'annuaire public.
     */
    public static function getLesMembres(): array
    {
        $sql = <<<SQL
            select nom, prenom,
                   if(autMail, email, 'Non communiqué') as mail,
                   ifnull(telephone, 'Non renseigné') as telephone,
                   ifnull(photo, 'Non renseignée') as photo,
                   saison
            from membre
            order by nom, prenom;
SQL;

        $select = new Select();
        return $select->getRows($sql);
    }

    /**
     * Récupère les données d'un membre à partir de son login.
     */
    public static function getByLogin(string $login): ?array
    {
        $sql = <<<SQL
            select id, login, email, nom, prenom from membre
            where login = :login;
SQL;
        $select = new Select();
        return $select->getRow($sql, ['login' => $login]);
    }

    /**
     * Récupère les données d'un membre à partir de son identifiant.
     */
    public static function getById(int $id): ?array
    {
        $sql = <<<SQL
            select id, nom, prenom, email, login, telephone, autMail
            from membre
            where id = :id;
SQL;
        $select = new Select();
        return $select->getRow($sql, ['id' => $id]);
    }

    /**
     * Récupère le nom de la photo associée à un membre.
     *
     * Cette méthode retourne uniquement la valeur enregistrée
     * dans la table membre. La vérification de l'existence
     * physique du fichier relève du service.
     *
     * @param int $id Identifiant du membre.
     * @return array|null
     */
    public static function getPhoto(int $id): ?array
    {
        $sql = <<<SQL
            select id, photo
            from membre
            where id = :id;
SQL;

        $select = new Select();
        $ligne = $select->getRow($sql, ['id' => $id]);
        return $ligne;
    }


    /**
     * Récupère les saisons distinctes des membres actifs.
     *
     * @return array<int, array<string, mixed>> Liste des saisons.
     */
    public static function getLesSaisons()
    {
        $sql = <<<SQL
        Select distinct saison
        from membre 
        order by saison;
SQL;
        $select = new Select();
        return $select->getRows($sql);
    }

    // ==========================================================
    // Gestion de la connexion
    // ==========================================================


    /**
     * Vérifie le mot de passe d'un membre.
     */
    public static function verifierPassword(int $id, string $password): bool
    {

        $sql = <<<SQL
            select password
            from membre
            where id = :id;
SQL;

        $select = new Select();
        $ligne = $select->getRow($sql, ['id' => $id]);
        return $ligne !== null && $ligne['password'] === hash('sha256', $password);
    }

    /**
     * Mémorise le membre connecté dans la session.
     */
    public static function connexion(array $membre): void
    {
        $_SESSION['membre'] = $membre;
    }


    /**
     * Déconnexion du membre courant.
     */
    public static function deconnexion(): void
    {
        unset($_SESSION['membre']);
    }





}