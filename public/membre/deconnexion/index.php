<?php
declare(strict_types=1);

use ClasseMetier\Membre;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

Membre::deconnexion();
header("location:/");
