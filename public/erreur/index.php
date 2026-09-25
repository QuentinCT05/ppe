<?php

declare(strict_types=1);

use ClasseTechnique\InterfaceSystem;
use ClasseTechnique\Journal;

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$titre = 'Erreur';
$type = 'erreur';
// les erreurs provenant d'une exception ne comportent que le message, titre et type ne peuvent prednre que des valeurs par défaut
if (isset($_SESSION['erreur']) && is_string($_SESSION['erreur'])) {

    // récupération du message d'erreur depuis la session et suppression de la variable de session
    $message = $_SESSION['erreur'];
    unset($_SESSION['erreur']);

    Journal::enregistrer($message, 'erreur');



} else {
    $message = 'Une erreur inconnue est survenue.';
}

$interface = new InterfaceSystem($titre, $message, $type);

$interface->afficher();