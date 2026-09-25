<?php
declare(strict_types=1);

use ClasseMetier\Epreuve;
use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";


// alimentation et affichage de l'interface
$page = new Page();
$page->setTitre("Les 4 saiosns")
    ->addScript("/composant/tinymce/tinymce.min.js")
    ->setDonnee('lesEpreuves', Epreuve::getAll())
    ->afficher();