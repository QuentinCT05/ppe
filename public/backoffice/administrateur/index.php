<?php
declare(strict_types=1);

use ClasseMetier\Administrateur;
use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// génération de la page
$page = new Page();
$page->setTitre("Définition des administrateurs")
    ->setDonnee("lesMembres", Administrateur::getLesMembres())
    ->setDonnee("lesAdministrateurs", Administrateur::getLesAdministrateurs())
    ->addScript("/composant/autocomplete/autocomplete.min.js")
    ->addStyle("/composant/autocomplete/autocomplete.css")
    ->afficher();
