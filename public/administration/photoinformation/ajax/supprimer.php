<?php
declare(strict_types=1);


use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;
use Service\FileManagerFactory;

// Chargement automatique des classes
require $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap/bootstrap.php';

// Vérification d'un appel AJAX par la méthode POST sécurisé par un jeton
Requete::exigerPost();

// Récupération du nom du fichier à supprimer
$nomFichier = Requete::postString('nomFichier');

// création d'un objet ImageManager en passant par une classe de configuration ou de fabrique (Factory Class)
$imageManager = FileManagerFactory::information();

// Suppression du fichier
if (!$imageManager->supprimer($nomFichier)) {
    ReponseJson::envoyerLesErreurs(['global' => ["Le fichier n'a pas pu être supprimé." ]]);
}

// Retour de la liste des images restantes
ReponseJson::envoyerLesDonnees($imageManager->getLesFichiers());