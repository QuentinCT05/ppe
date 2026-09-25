<?php
declare(strict_types=1);

use ClasseMetier\Administrateur;
use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// Vérification d'un appel AJAX par la méthode POST sans jeton
Requete::exigerPostSansJeton();

$idAdministrateur = Requete::postInt('idAdministrateur');
$repertoire = Requete::postString('repertoire');

// demande d'ajout d'un droit pour un administrateur
Administrateur::ajouterDroit($idAdministrateur, $repertoire);

ReponseJson::envoyerMessage("Droit ajouté");


