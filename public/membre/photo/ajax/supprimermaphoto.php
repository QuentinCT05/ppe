<?php
declare(strict_types=1);

use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;
use Service\ServiceMembre;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap/bootstrap.php';

// Vérification d'un appel AJAX POST sécurisé
Requete::exigerPost();

$service = new ServiceMembre();

// suppression de la photo dans la table membre
$service->supprimerMaPhoto();

// réponse du serveur : nom du fichier stocké
echo json_encode(['success' => "Votre photo a été supprimée avec succès, elle n'apparait plus dans l'annuaire"], JSON_UNESCAPED_UNICODE);