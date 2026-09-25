<?php
declare(strict_types=1);

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

use ClasseTechnique\InterfaceSystem;

$titre  = 'Droits inssufisants pour accéder à cette page';

$message = <<<TXT
Rappel — article 323-1 du code pénal français : 
Le fait d'accéder ou de se maintenir, frauduleusement, dans tout ou partie d'un système de traitement automatisé de données est puni de deux ans d'emprisonnement et de 60 000 euros d'amende. 

Lorsqu'il en est résulté soit la suppression ou la modification de données contenues dans le système, soit une altération du fonctionnement de ce système, la peine est de trois ans d'emprisonnement et de 100 000 euros d'amende.
TXT;

$type = "erreur";

$interface = new InterfaceSystem($titre, $message,$type);

$interface->afficher();