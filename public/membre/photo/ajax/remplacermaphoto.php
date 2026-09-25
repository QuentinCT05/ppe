<?php
declare(strict_types=1);

use Service\ServiceMembre;
use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap/bootstrap.php';

// Vérification d'un appel AJAX POST sécurisé
Requete::exigerPost();

// récupération des données
$file = Requete::getFile('fichier');

// Appel du service métier
$serviceMembre = new ServiceMembre();

if (!$serviceMembre->enregistrerMaPhoto($file)) {
    ReponseJson::envoyerLesErreurs($serviceMembre->getErrors());
}

ReponseJson::envoyerMessage("Votre photo est enregistrée.");