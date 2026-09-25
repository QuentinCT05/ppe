<?php
declare(strict_types=1);

use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// Si l'utilisateur est déja connecté, on le redirige vers son profil
if (isset($_SESSION['membre'])) {
    header("location:/");
    exit;
}

// Génération de la page
$page = new Page();
$page->avecJeton()
->afficher();
