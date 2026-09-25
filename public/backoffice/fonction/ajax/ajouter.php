<?php
declare(strict_types=1);

use ClasseMetier\Fonction;

use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap/bootstrap.php';

// Vérification d'un appel AJAX par la méthode POST sécurisé par un jeton
Requete::exigerPostSansJeton();

// récupération des données transmises
$columns = Requete::postArray('columns');

// création de l'objet métier
$fonction = new Fonction();

// ajout de la catégorie
$resultat = $fonction->add($columns);

if ($resultat === true) {
    ReponseJson::envoyerMessage("Fonction ajoutée");
}

// en cas d'erreur
ReponseJson::envoyerLesErreurs($fonction->getErrors());