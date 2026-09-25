<?php
declare(strict_types=1);

use ClasseMetier\Categorie;
use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

$page = new Page();

$page->setDonnee("lesCategories", Categorie::getAll())
    ->addScript("/composant/html2pdf/html2pdf.bundle.min.js")
    ->afficher();