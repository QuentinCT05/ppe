<?php
declare(strict_types=1);

use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

$titre = '5 km de la hotoie';

$page = new Page();

$page->setTitre($titre)
    ->afficher();


