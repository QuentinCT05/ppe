<?php
declare(strict_types=1);

use ClasseMetier\Membre;
use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap/bootstrap.php';

// Vérification d'un appel AJAX par la méthode POST sécurisé par un jeton
Requete::exigerPost();

// récupération des données transmises
$id = Requete::postInt('primaryKey');
$columns = Requete::postArray('columns');

// création de l'objet métier
$membre = new Membre();

// modification du membre
$resultat = $membre->modify($id, $columns);

if ($resultat === true) {
    ReponseJson::envoyerMessage("Membre modifié");
}

// en cas d'erreur
ReponseJson::envoyerLesErreurs($membre->getErrors());
