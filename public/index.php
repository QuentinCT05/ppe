<?php
declare(strict_types=1);

use ClasseMetier\Classement;
use ClasseMetier\Epreuve;
use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// alimentation de l'interface
// chagement des classements et de la prochaine épreuve
$page = new Page();
$page->setDonnee('laProchaineEpreuve', Epreuve::getProchaineEpreuve())
    ->afficher();
