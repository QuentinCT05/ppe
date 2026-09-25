<?php
declare(strict_types=1);

use ClasseMetier\Membre;
use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// alimentation et affichage de l'interface
$page = new Page();
$page->setTitre("Liste des membres")
    ->setDonnee('lesMembres', Membre::getAll())
    ->afficher();


