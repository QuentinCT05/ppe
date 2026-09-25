<?php
declare(strict_types=1);

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

use ClasseTechnique\Page;

// chargement des données
$titre = "Calendriers F.F.A. des courses hors stades";

// chargement interface
$page = new Page();
$page ->setTitre($titre)
    ->afficher();

