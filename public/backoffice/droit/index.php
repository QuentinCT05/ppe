<?php
declare(strict_types=1);

use ClasseMetier\Administrateur;
use ClasseMetier\Fonction;
use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// génération de la page
$page = new Page();
$page->setTitre("Définition des administrateurs")
    ->setDonnee("lesFonctions", Fonction::getAll())
    ->setDonnee("lesAdministrateurs", Administrateur::getLesAdministrateurs())
    ->afficher();
