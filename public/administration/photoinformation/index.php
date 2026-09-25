<?php

declare(strict_types=1);

use ClasseTechnique\Config;
use ClasseTechnique\ImageManager;
use ClasseTechnique\Page;
use Service\FileManagerFactory;

// activation du chargement dynamique des ressources
require $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap/bootstrap.php';

// création d'un objet ImageManager en passant par une classe de configuration ou de fabrique (Factory Class)
$imageManager = FileManagerFactory::information();

// alimentation et affichage de l'interface
$page = new Page();

$page->setTitre("Téléversement d'une image")
    ->setDonnee('lesFichiers',  $imageManager->getLesFichiers())
    ->avecJeton()
    ->afficher();