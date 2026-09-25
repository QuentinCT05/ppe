<?php
declare(strict_types=1);

use ClasseMetier\Administrateur;
use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// génération de la page
$page = new Page();
$page->setTitre("Espace Membre")
     ->setDonnee("lesFonctions", Administrateur::getLesFonctionsAutorisees($_SESSION['membre']['id']))
    ->afficher();
