<?php
declare(strict_types=1);

use ClasseMetier\Membre;
use ClasseTechnique\Requete;
use ClasseTechnique\UserException;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap/bootstrap.php';

// Vérification d'un appel AJAX POST sécurisé
Requete::exigerPost();

// récupération des données transmises
$login = Requete::postString('login');
$password = Requete::postString('password');

// vérification du login
if (!preg_match('/^[a-zA-Z]{2,}$/', $login)) {
    throw new UserException('Nom d’utilisateur et/ou mot de passe incorrect.');
} else {
    $membre = Membre::getByLogin($login);
    if (!$membre) {
        throw new UserException('Nom d’utilisateur et/ou mot de passe incorrect.');
    }
}

// vérification du mot de passe
if (!Membre::verifierPassword($membre['id'], $password)) {
    throw new UserException('Nom d’utilisateur et/ou mot de passe incorrect.');
}

// Mémorisation de la connexion
Membre::connexion($membre);

// Vers quelle page faut-il être redirigé ?
if(isset($_SESSION['url'])) {
    $url = $_SESSION['url'];
    unset($_SESSION['url']);
} else {
    $url = '/';
}

$reponse = ['success' => $url];
echo json_encode($reponse, JSON_UNESCAPED_UNICODE);

