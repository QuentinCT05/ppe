<?php
declare(strict_types=1);

use ClasseMetier\Administrateur;
use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// Vérification d'un appel AJAX par la méthode POST sans jeton
Requete::exigerPostSansJeton();

$id = Requete::postInt('idAdministrateur');

ReponseJson::envoyerLesDonnees(Administrateur::getLesFonctionsAutorisees($id));
