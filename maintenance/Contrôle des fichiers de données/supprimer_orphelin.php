<?php


declare(strict_types=1);

/**
 * Suppression des fichiers orphelins.
 *
 * Les fichiers à supprimer sont définis dans orphelins.json.
 * Ce script est exclusivement destiné à une exécution CLI.
 */

// -----------------------------------------------------------------------------
// Vérification du mode CLI
// -----------------------------------------------------------------------------

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Erreur : ce script doit être exécuté en ligne de commande.\n");
    exit(1);
}

// -----------------------------------------------------------------------------
// Chargement et validation du fichier JSON
// -----------------------------------------------------------------------------

$fichierOrphelins = __DIR__ . DIRECTORY_SEPARATOR . 'orphelins.json';

if (!is_file($fichierOrphelins)) {
    fwrite(STDERR, "Erreur : fichier des orphelins introuvable : {$fichierOrphelins}\n");
    exit(1);
}

$contenu = file_get_contents($fichierOrphelins);

if ($contenu === false || trim($contenu) === '') {
    fwrite(STDERR, "Erreur : le fichier des orphelins est vide.\n");
    exit(1);
}

try {
    /** @var array<int, array{controle: string, fichier: string, repertoire: string}> $orphelins */
    $orphelins = json_decode($contenu, true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    fwrite(STDERR, "Erreur de lecture du JSON : " . $e->getMessage() . "\n");
    exit(1);
}

if (empty($orphelins)) {
    echo "Aucun fichier orphelin à traiter.\n";
    exit(0);
}

// -----------------------------------------------------------------------------
// Affichage des fichiers à supprimer
// -----------------------------------------------------------------------------

echo "\n============================================================\n";
echo " FICHIERS ORPHELINS À SUPPRIMER\n";
echo "============================================================\n\n";

foreach ($orphelins as $orphelin) {
    $controle = $orphelin['controle'] ?? 'INCONNU';
    $fichier = $orphelin['fichier'] ?? 'N/A';
    echo "  - [{$controle}] {$fichier}\n";
}

$total = count($orphelins);
echo "\nNombre de fichiers à supprimer : {$total}\n\n";

// -----------------------------------------------------------------------------
// Demande de confirmation
// -----------------------------------------------------------------------------

echo "ATTENTION : cette opération est irréversible.\n";
echo "Voulez-vous supprimer ces fichiers ? [o/N] : ";

fflush(STDOUT);

$confirmation = trim((string)fgets(STDIN));

if (!in_array(strtolower($confirmation), ['o', 'oui'], true)) {
    echo "\nSuppression annulée. Aucun fichier n'a été supprimé.\n\n";
    exit(0);
}

// -----------------------------------------------------------------------------
// Suppression des fichiers
// -----------------------------------------------------------------------------

echo "\nSuppression des fichiers...\n\n";

$nbSuppression = 0;
$nbErreur = 0;

foreach ($orphelins as $orphelin) {
    if (empty($orphelin['fichier']) || empty($orphelin['repertoire'])) {
        fwrite(STDERR, "Erreur : entrée invalide dans le JSON.\n");
        $nbErreur++;
        continue;
    }

    $repertoire = rtrim($orphelin['repertoire'], '/\\');
    $chemin = $repertoire . DIRECTORY_SEPARATOR . $orphelin['fichier'];

    if (!file_exists($chemin)) {
        fwrite(STDERR, "Avertissement : fichier introuvable sur le disque : {$chemin}\n");
        $nbErreur++;
        continue;
    }

    if (@unlink($chemin)) {
        echo "Fichier supprimé : {$orphelin['fichier']}\n";
        $nbSuppression++;
    } else {
        fwrite(STDERR, "Erreur : impossible de supprimer le fichier : {$chemin}\n");
        $nbErreur++;
    }
}

// -----------------------------------------------------------------------------
// Bilan
// -----------------------------------------------------------------------------

echo "\n------------------------------------------------------------\n";
echo "Fichiers supprimés : {$nbSuppression}\n";
echo "Erreurs / Absents  : {$nbErreur}\n";
echo "------------------------------------------------------------\n\n";

exit($nbErreur === 0 ? 0 : 1);