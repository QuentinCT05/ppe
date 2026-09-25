<?php

declare(strict_types=1);

/**
 * Vérification bidirectionnelle de la structure de la base de données.
 *
 * Compare la BDD avec structure.json pour détecter :
 * - Les tables / colonnes attendues mais absentes de la BDD ;
 * - Les tables / colonnes présentes en BDD mais absentes du schéma.
 *
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
// Lecture de la configuration
// -----------------------------------------------------------------------------

$fichierStructure = __DIR__ . DIRECTORY_SEPARATOR . 'structure.json';

if (!is_file($fichierStructure)) {
    fwrite(STDERR, "Erreur : fichier de structure introuvable : {$fichierStructure}\n");
    exit(1);
}

try {
    $contenu = file_get_contents($fichierStructure);

    if ($contenu === false || trim($contenu) === '') {
        fwrite(STDERR, "Erreur : le fichier structure.json est vide.\n");
        exit(1);
    }

    $configuration = json_decode($contenu, true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    fwrite(STDERR, "Erreur de syntaxe JSON dans structure.json : " . $e->getMessage() . "\n");
    exit(1);
}

if (!isset($configuration['database'], $configuration['tables']) || !is_array($configuration['tables'])) {
    fwrite(STDERR, "Erreur : la structure du fichier structure.json est invalide.\n");
    exit(1);
}

$config    = $configuration['database'];
$structure = $configuration['tables'];

// -----------------------------------------------------------------------------
// Connexion à la base de données
// -----------------------------------------------------------------------------

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $config['host'] ?? '127.0.0.1',
        $config['port'] ?? '3306',
        $config['name'] ?? ''
    );

    $pdo = new PDO(
        $dsn,
        $config['user'] ?? '',
        $config['password'] ?? '',
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    fwrite(STDERR, "Erreur de connexion à la base de données : " . $e->getMessage() . "\n");
    exit(1);
}

// -----------------------------------------------------------------------------
// En-tête et inspection BDD
// -----------------------------------------------------------------------------

echo "\n============================================================\n";
echo " VÉRIFICATION DE LA STRUCTURE DE LA BDD\n";
echo "============================================================\n\n";

try {
    $baseCourante = $pdo->query("SELECT DATABASE()")->fetchColumn();
    $utilisateur  = $pdo->query("SELECT USER()")->fetchColumn();
    $tablesBase   = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    fwrite(STDERR, "Erreur lors de l'inspection de la BDD : " . $e->getMessage() . "\n");
    exit(1);
}

echo "Base courante : {$baseCourante}\n";
echo "Utilisateur   : {$utilisateur}\n\n";

// Helper pour échapper les noms de tables
$echapperSql = static fn(string $nom): string => '`' . str_replace('`', '``', $nom) . '`';

$nombreErreurs      = 0;
$nombreAvertissements = 0;

// -----------------------------------------------------------------------------
// 1. Contrôle des tables et colonnes attendues (Manquants)
// -----------------------------------------------------------------------------

echo "------------------------------------------------------------\n";
echo " 1. Contrôle des éléments attendus (Conformité & Manquants)\n";
echo "------------------------------------------------------------\n\n";

$tablesAttendues = array_keys($structure);

foreach ($structure as $table => $colonnesAttendues) {

    if (!in_array($table, $tablesBase, true)) {
        echo "[ERREUR] Table absente : {$table}\n";
        $nombreErreurs++;
        continue;
    }

    try {
        $tableSql = $echapperSql((string) $table);
        $cmd      = $pdo->query("SHOW COLUMNS FROM {$tableSql}");

        $colonnesBase = array_column($cmd->fetchAll(), 'Field');
    } catch (PDOException $e) {
        echo "[ERREUR] Impossible de lire les colonnes de '{$table}' : " . $e->getMessage() . "\n";
        $nombreErreurs++;
        continue;
    }

    $erreurTable = false;

    // Colonnes manquantes
    foreach ($colonnesAttendues as $colonne) {
        if (!in_array($colonne, $colonnesBase, true)) {
            echo "[ERREUR] Colonne absente : {$table}.{$colonne}\n";
            $nombreErreurs++;
            $erreurTable = true;
        }
    }

    // Colonnes en trop dans une table existante
    $colonnesEnTrop = array_diff($colonnesBase, $colonnesAttendues);
    foreach ($colonnesEnTrop as $colonneExtra) {
        echo "[AVERTISSEMENT] Colonne non répertoriée : {$table}.{$colonneExtra}\n";
        $nombreAvertissements++;
        $erreurTable = true;
    }

    if (!$erreurTable) {
        echo "[OK] {$table}\n";
    }
}

// -----------------------------------------------------------------------------
// 2. Recherche des tables en trop dans la BDD
// -----------------------------------------------------------------------------

echo "\n------------------------------------------------------------\n";
echo " 2. Détection des tables non répertoriées (Superflues)\n";
echo "------------------------------------------------------------\n\n";

$tablesEnTrop = array_diff($tablesBase, $tablesAttendues);

if (empty($tablesEnTrop)) {
    echo "Aucune table superflue détectée.\n";
} else {
    foreach ($tablesEnTrop as $tableExtra) {
        echo "[AVERTISSEMENT] Table non répertoriée dans le schéma : {$tableExtra}\n";
        $nombreAvertissements++;
    }
}

// -----------------------------------------------------------------------------
// Bilan final
// -----------------------------------------------------------------------------

echo "\n------------------------------------------------------------\n";
echo " BILAN FINAL\n";
echo "------------------------------------------------------------\n";
echo "Erreurs (Éléments manquants)        : {$nombreErreurs}\n";
echo "Avertissements (Éléments en trop)   : {$nombreAvertissements}\n";
echo "------------------------------------------------------------\n\n";

if ($nombreErreurs === 0 && $nombreAvertissements === 0) {
    echo "Structure strictement conforme.\n\n";
    exit(0);
}

if ($nombreErreurs === 0) {
    echo "Structure fonctionnelle mais des éléments non répertoriés sont présents.\n\n";
    exit(0);
}

echo "La structure de la BDD est incomplète.\n\n";
exit(1);