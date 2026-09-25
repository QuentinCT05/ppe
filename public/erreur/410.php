<?php

declare(strict_types=1);

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

use ClasseTechnique\InterfaceSystem;

$titre = 'Page définitivement supprimée';

$message = 'La page demandée a été définitivement supprimée du site.';

$type = 'information';

$interface = new InterfaceSystem($titre, $message, $type);

$interface->afficher();
