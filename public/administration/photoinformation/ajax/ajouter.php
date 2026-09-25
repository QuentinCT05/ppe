<?php
declare(strict_types=1);

use ClasseTechnique\InputFileImg;

use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;
use Service\FileManagerFactory;

// Chargement automatique des classes
require $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap/bootstrap.php';

// Vérification d'un appel AJAX par la méthode POST sécurisé par un jeton
Requete::exigerPost();

// création d'un objet ImageManager en passant par une classe de configuration ou de fabrique (Factory Class)
$imageManager = FileManagerFactory::information();

// récupération du fichier téléversé
$fichier = Requete::getFile('fichier');

// Création de l'objet fichier image
$file = new InputFileImg($fichier);

// Paramètrage de cet objet par rapport au contexte d'utilisation : requis, renommage
$file->setRequired(false);

// Ajout de l'image
if (!$imageManager->ajouter($file, false)) {
    ReponseJson::envoyerLesErreurs(['global' => [ $file->getValidationMessage()]]);
}


// Retour de la liste des images présentes
ReponseJson::envoyerLesDonnees($imageManager->getLesFichiers());