<?php

declare(strict_types=1);

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

use ClasseTechnique\InterfaceSystem;

$titre = 'Accès interdit';
$message = <<<TXT
    Vous n'avez pas les droits suffisants pour accéder à cette page.
    Veuillez vous connecter avec un compte ayant les droits nécessaires.
TXT;

$type = 'avertissement';

$interface = new InterfaceSystem($titre, $message, $type);

$interface->afficher();