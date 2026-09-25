<?php
declare(strict_types=1);

use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

$titre = 'Parcours 5 km - 4 Saisons';

$page = new Page();

$page->setTitre($titre)
    ->afficher();


