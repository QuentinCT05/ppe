<?php
declare(strict_types=1);

use ClasseMetier\ControleAcces;
use ClasseTechnique\Config;
use ClasseTechnique\Erreur;
use ClasseTechnique\Ip;
use ClasseTechnique\Journal;
use ClasseTechnique\UserException;

// ==========================================================
// Initialisation générale
// ==========================================================

date_default_timezone_set('Europe/Paris');

// ==========================================================
// Gestion de session
// ==========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// ==========================================================
// Définition des chemins du projet
// ==========================================================

// DOSSIER_RACINE complète du projet
define('DOSSIER_RACINE', dirname(__DIR__));

// Répertoire public accessible par le navigateur
define('DOSSIER_WWW', DOSSIER_RACINE . DIRECTORY_SEPARATOR . 'public');


// Répertoire contenant les fichiers de configuration
define('DOSSIER_CONFIG', DOSSIER_RACINE . DIRECTORY_SEPARATOR . 'config');

// constantes.php pour les dossiers comprenant des resources externes
const DOSSIER_CLASSEMENT  = DOSSIER_WWW . '/data/classement';
const DOSSIER_PHOTO_MEMBRE = DOSSIER_WWW . '/data/photomembre';
const DOSSIER_PHOTO_INFORMATION = DOSSIER_WWW . '/data/photoinformation';

// ==========================================================
// Chargement automatique des classes
// ==========================================================

require DOSSIER_RACINE . '/vendor/autoload.php';

// ==========================================================
// Gestion globale des erreurs
// ==========================================================

// initialisation d'un buffer de sortie pour éviter l'affichage d'erreurs avant le traitement
ob_start();
Erreur::installerGestionnaire();


// ==========================================================
// Chargement des contraintes SQL
// ==========================================================

try {

    $contraintes = Config::chargerPhp('contrainte');

    Erreur::definirLesContraintes($contraintes);

} catch (Exception $e) {

    // Pas de contrainte configurée
    // ou configuration absente :
    // on laisse l'application démarrer.
}



// Vérification globale de l'accès
ControleAcces::verifier();

