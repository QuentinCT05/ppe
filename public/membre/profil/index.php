<?php
declare(strict_types=1);

use ClasseMetier\Membre;
use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// chargement des données
$data = Membre::getById($_SESSION['membre']['id']);

// génération de la page
$page = new Page();

$page->setTitre("Mon profil")
    ->setDonnee("data", $data)
    ->avecJeton()
    ->afficher();