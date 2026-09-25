<?php

declare(strict_types=1);

namespace Service;

use ClasseMetier\Membre;
use ClasseTechnique\InputFileImg;
use ClasseTechnique\ImageManager;
use ClasseTechnique\UserException;

/**
 * ServiceMembre
 *
 * Gestion de la cohérence entre la table membre et le stockage physique des photos.;
 *
 * @author Guy Verghote
 * @version 2026.1
 */
class ServiceMembre
{
    // Gestionnaire des images.
    private ImageManager $imageManager;

    // Objet métier Membre.
    private Membre $membre;


    /**
     * Constructeur.
     */
    public function __construct()
    {
        $this->imageManager = FileManagerFactory::membre();
        $this->membre = new Membre();
    }


    /**
     * Retourne la liste des clubs.
     *
     * La présence physique du photo est ajoutée
     * pour faciliter l'utilisation côté interface.
     *
     * @return array
     */
    public function getLesMembres(): array
    {
        $lesMembres = Membre::getLesMembres();
        foreach ($lesMembres as $index => $membre) {
            // pour obliger le cache du navigateur à être rafraichi à chaque modification du photo
            $lesMembres[$index]['photoVersion'] = null;

            $present = !empty($membre['photo']) && $this->imageManager->existe($membre['photo']);
            if ($present) {
                $fichier = $this->imageManager->getRepertoire() . DIRECTORY_SEPARATOR . $membre['photo'];
                $temps = filemtime($fichier);
                $lesMembres[$index]['photoVersion'] = $temps === false ? null : $temps;
            }
            $lesMembres[$index]['photoPresent'] = $present;
        }
        return $lesMembres;
    }


    // La création d'un membre s'effectue toujours sans sa photo
    // Le service n'offre donc pas de méthode 'ajouter'
    // par contre, il doit être possible de supprimer la photo d'un membre existant.

    public function supprimerMaPhoto(): bool
    {
        // Récupération des coordonnées du membre.
        $membre = $this->getMaPhoto();
        if ($membre === false) {
            $this->membre->addServiceError('global', "Le membre n'existe pas.");
            return false;
        }

        // Si le membre ne possède pas encore de photo
        if ($membre['photo'] === null) {
            $this->membre->addServiceError('global', "Ce membre ne dispose pas d'une photo.");
            return false;
        }

        // La base de données est prioritaire :
        // on supprime d'abord la référence vers la photo.
        $id = $_SESSION['membre']['id'];
        if (!$this->membre->modify($id, ['photo' => null])) {
            $this->membre->addServiceError('global', "La photo n'a pas pu être supprimée.");
            return false;
        }

        // La base est maintenant cohérente.
        // La suppression physique est secondaire.
        // Une éventuelle erreur laisse simplement un fichier orphelin.
        $this->imageManager->supprimer($membre['photo']);

        return true;
    }


    /**
     * Récupère la photo associée à un membre.
     *
     * Cette méthode reste spécifique, car elle vérifie
     * également la présence physique du fichier.
     */
    public function getMaPhoto(): ?array
    {
        $id = $_SESSION['membre']['id'];
        $ligne = Membre::getPhoto($id);
        if ($ligne === null) {
            return null;
        }
        $ligne['present'] = !empty($ligne['photo']) && is_file(DOSSIER_PHOTO_MEMBRE . '/' . $ligne['photo']) ? 1 : 0;
        return $ligne;
    }


    /**
     * Ajoute ou remplace la photo d'un membre.
     *
     * Si le membre ne possède pas encore de photo, un ajout est réalisé.
     *
     * Sinon le fichier existant est remplacé.
     *
     * @param array $file Tableau $_FILES['photo'].
     * @return bool true si l'opération a réussi, false sinon.
     * @throws UserException
     */
    public function enregistrerMaPhoto(array $file): bool
    {
        $nouvellePhoto = new InputFileImg($file);

        // Une photo est obligatoire pour cette opération.
        $nouvellePhoto->setRequired(true);

        // Récupération de l'identifiant du membre connecté
        $id = $_SESSION['membre']['id'];

        // Récupération éventuelle de l'ancienne photo
        $membre = Membre::getPhoto($id);
        if ($membre === false) {
            $this->membre->addServiceError('photo', "Le membre n'existe pas.");
            return false;
        }

        // Si le membre ne possède pas encore de photo, on effectue un ajout
        if (empty($membre['photo']) || !is_file(DOSSIER_PHOTO_MEMBRE . '/' . $membre['photo'])) {
            if (!$this->imageManager->ajouter($nouvellePhoto)) {
                $this->membre->addServiceError('photo', $nouvellePhoto->getValidationMessage());
                return false;
            }
            $nomPhoto = (string)$nouvellePhoto->getValue();
            if (!$this->membre->modify($id, ['photo' => $nomPhoto])) {
                // si SQL échoue, suppression du fichier créé.
                $this->imageManager->supprimer($nomPhoto);
                return false;
            }
            return true;
        }

        // le membre possédait une photo, on procède à son remplacement.
        if (!$this->imageManager->remplacer($membre['photo'], $nouvellePhoto)) {
            $this->membre->addServiceError('photo', $nouvellePhoto->getValidationMessage());
            return false;
        }
        return true;
    }

    /**
     * Retourne les erreurs métier.
     */
    public function getErrors(): array
    {
        return $this->membre->getErrors();
    }
}