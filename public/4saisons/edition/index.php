<?php
declare(strict_types=1);

use ClasseMetier\Epreuve;
use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";


// génération de la page
$page = new Page();

$page->setDonnee("lesEditions", Epreuve::getLesProchainesEpreuves())
    ->setDonnee("lesHoraires", require DOSSIER_RACINE . "/config/4saisons.php")
    ->afficher();