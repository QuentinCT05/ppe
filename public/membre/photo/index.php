<?php
declare(strict_types=1);

use Service\ServiceMembre;
use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// chargement des données
$serviceMembre = new ServiceMembre();
$photo = $serviceMembre->getMaPhoto();

// génération de la page
$page = new Page();

$page->setTitre("Ma Photo")
    ->setDonnee("photo", $photo)
    ->avecJeton()
    ->afficher();
