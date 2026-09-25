<?php
declare(strict_types=1);

use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// Récupération des fonctions d'administration depuis le fichier JSON
$lesFonctions = json_decode(file_get_contents("config/menuhorizontal.json"), true);

// génération de la page
$page = new Page();
$page->setTitre("Espace d'administration du site")
    ->setDonnee("lesFonctions", $lesFonctions)
    ->afficher();