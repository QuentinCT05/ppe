<?php
declare(strict_types=1);

use ClasseMetier\Membre;
use Service\ServiceMembre;
use ClasseTechnique\Page;

//* noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// génération de la page
$page = new Page();
$serviceMembre = new ServiceMembre();

$page->setTitre("Annuaire des membres")
    ->setDonnee("lesMembres", $serviceMembre->getLesMembres())
    ->setDonnee("lesSaisons", Membre::getLesSaisons())
    ->afficher();