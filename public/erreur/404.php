<?php

declare(strict_types=1);

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

use ClasseTechnique\InterfaceSystem;

$url = $_SERVER['REQUEST_URI'] ?? '/';

$titre = 'Page introuvable';
$message = <<<TXT
  La page demandée n'existe pas.
  URL demandée : $url
TXT;

$type = 'erreur';

$interface = new InterfaceSystem($titre, $message, $type);

$interface->afficher();