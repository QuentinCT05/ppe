<?php
declare(strict_types=1);

namespace ClasseMetier;

use ClasseTechnique\Database;
use ClasseTechnique\Select;

/**
 * Classe Administrateur
 * Cette table permet de gérer les administrateurs de l'application adminstrateur(id)
 * La table ne possédant qu'un seul attribut id, elle ne peut pas être gérée avec la classe Table
 */

class Administrateur
{

    /*
     * Retourne l'ensemble des membres administrateur
     * @return array
     *
     */
    public static function getLesAdministrateurs(): array
    {
        $sql = <<<SQL
              Select membre.id, concat(nom, ' ', prenom) as nom
              from membre join administrateur on membre.id = administrateur.id 
              order by nom; 
SQL;
        $select = new Select();
        return $select->getRows($sql);
    }


    /**
     * Retourne les fonctions autorisées pour un administrateur
     * @param int $idAdministrateur
     * @return array
     */
    public static function getLesFonctionsAutorisees(int $idAdministrateur): array
    {
        $sql = <<<SQL
           Select nom, fonction.repertoire
           from fonction
               join droit on fonction.repertoire = droit.repertoire
               where idAdministrateur = :idAdministrateur
           order by nom;

SQL;
        $select = new Select();
        return $select->getRows($sql, ["idAdministrateur" => $idAdministrateur]);
    }


    /**
     * Retourne l'ensemble des membres qui ne sont pas administrateur
     * @return array
     */
    public static function getLesMembres(): array
    {
        $sql = <<<SQL
            Select id, concat(nom, ' ', prenom) as nom
            from membre where id not in (select id from administrateur)  
            order by nom; 
SQL;
        $select = new Select();
        return $select->getRows($sql);
    }

    /**
     * Vérifie si un membre est administrateur
     * @param $id
     * @return int|mixed
     */
    public static function estUnAdministrateur($id)
    {
        $sql = "select 1 from administrateur where id = :id";
        $select = new Select();

        $reponse = $select->getValue($sql, ["id" => $id]);

        return $reponse ?? 0;
    }

    /**
     * Vérifie si un administrateur est autorisé à lancer les script d'un répertoire
     * @param int $id
     * @param string $repertoire
     * @return bool
     */
    public static function peutAdministrer(int $id, string $repertoire): bool
    {
        $sql = <<<SQL
                select 1 from droit 
                where idAdministrateur = :id 
                and repertoire = :repertoire;
SQL;
        $select = new Select();
        $reponse = $select->getValue($sql, ["id" => $id, "repertoire" => $repertoire]);
        return (bool) ($reponse ?? false);
    }


    /**
     * Ajoute un administrateur
     * @param int $id
     * @return void
     */
    public static function ajouterAdministrateur(int $id): void
    {
            $db = Database::getInstance();
            $sql = " insert into administrateur (id) values (:id)";
            $curseur = $db->prepare($sql);
            $curseur->bindParam('id', $id);
            $curseur->execute();
    }

    /**
     * Supprime un administrateur
     * @param int $id
     * @return void
     */
    public static function supprimerAdministrateur(int $id): void
    {
            $db = Database::getInstance();
            $sql = "delete from administrateur where id = :id;";
            $curseur = $db->prepare($sql);
            $curseur->bindParam('id', $id);
            $curseur->execute();
    }

    /**
     * Ajoute un droit à un administrateur
     * @param int $idAdministrateur
     * @param string $repertoire
     * @return void
     */
    public static function ajouterDroit(int $idAdministrateur, string $repertoire)
    {
            $db = Database::getInstance();
            $sql = " insert into droit (repertoire, idAdministrateur) values (:repertoire, :idAdministrateur)";
            $curseur = $db->prepare($sql);
            $curseur->bindParam('repertoire', $repertoire);
            $curseur->bindParam('idAdministrateur', $idAdministrateur);
            $curseur->execute();
    }

    /**
     * Supprime un droit à un administrateur
     * @param int $idAdministrateur
     * @param string $repertoire
     * @return void
     */
    public static function supprimerDroit(int $idAdministrateur, string $repertoire)
    {
            $db = Database::getInstance();
            $sql = "delete from droit where repertoire = :repertoire and idAdministrateur = :idAdministrateur;";
            $curseur = $db->prepare($sql);
            $curseur->bindParam('repertoire', $repertoire);
            $curseur->bindParam('idAdministrateur', $idAdministrateur);
            $curseur->execute();
    }

    /**
     * Supprime tous les droits d'un administrateur
     * @param int $idAdministrateur
     * @return void
     */
    public static function supprimerTousLesDroits(int $idAdministrateur): void
    {
            $db = Database::getInstance();
            $sql = "delete from droit where idAdministrateur = :idAdministrateur;";
            $curseur = $db->prepare($sql);
            $curseur->bindParam('idAdministrateur', $idAdministrateur);
            $curseur->execute();
    }

    /**
     * Ajoute tous les droits à un administrateur
     * @param int $idAdministrateur
     * @return void
     */
    public static function ajouterTousLesDroits(int $idAdministrateur): void
    {
        try {
            $db = Database::getInstance();
            // on supprime préalablement tous les droits
            $sql = <<<SQL
	        delete from droit where idAdministrateur = :idAdministrateur;
SQL;
            $curseur = $db->prepare($sql);
            $curseur->bindParam('idAdministrateur', $idAdministrateur);
            $curseur->execute();
            // on ajoute ensuite tous les droits
            $sql = <<<SQL
	        insert into droit(idAdministrateur, repertoire) select :idAdministrateur, repertoire from fonction;
SQL;
            $curseur = $db->prepare($sql);
            $curseur->bindParam('idAdministrateur', $idAdministrateur);
            $curseur->execute();
        } catch (Exception $e) {
            throw new UserException($e->getMessage());
        }
    }
}
