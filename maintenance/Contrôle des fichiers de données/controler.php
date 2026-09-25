<?php

declare(strict_types=1);

/**
 * Contrôle générique de cohérence des fichiers.
 *
 * Pour chaque contrôle défini dans config.json :
 * - vérifie les fichiers référencés dans la base mais absents du disque ;
 * - vérifie les fichiers présents sur le disque mais non référencés dans la base.
 *
 * Les fichiers orphelins sont enregistrés dans orphelins.json.
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
// Chargement de la configuration
// -----------------------------------------------------------------------------

$fichierConfiguration = __DIR__ . DIRECTORY_SEPARATOR . 'config.json';

if (!is_file($fichierConfiguration)) {
    fwrite(STDERR, "Erreur : fichier de configuration introuvable : {$fichierConfiguration}\n");
    exit(1);
}

$contenuConfig = file_get_contents($fichierConfiguration);

if ($contenuConfig === false || trim($contenuConfig) === '') {
    fwrite(STDERR, "Erreur : le fichier de configuration est vide.\n");
    exit(1);
}

try {
    $config = json_decode($contenuConfig, true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    fwrite(STDERR, "Erreur de syntaxe JSON dans config.json : " . $e->getMessage() . "\n");
    exit(1);
}

if (!isset($config['base_de_donnees'], $config['controles']) || !is_array($config['controles'])) {
    fwrite(STDERR, "Erreur : la structure du fichier config.json est invalide.\n");
    exit(1);
}

// -----------------------------------------------------------------------------
// Connexion à la base de données
// -----------------------------------------------------------------------------

$bdd = $config['base_de_donnees'];

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $bdd['host'] ?? '127.0.0.1',
        $bdd['port'] ?? '3306',
        $bdd['nom'] ?? ''
    );

    $pdo = new PDO(
        $dsn,
        $bdd['utilisateur'] ?? '',
        $bdd['mot_de_passe'] ?? '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    fwrite(STDERR, "Erreur de connexion à la base de données : " . $e->getMessage() . "\n");
    exit(1);
}

// -----------------------------------------------------------------------------
// Fichier contenant les fichiers orphelins
// -----------------------------------------------------------------------------

$fichierOrphelins = __DIR__ . DIRECTORY_SEPARATOR . 'orphelins.json';
$orphelins = [];

// Helper pour échapper les identifiants SQL (tables / colonnes)
$echapperSql = static fn(string $nom): string => '`' . str_replace('`', '``', $nom) . '`';

// -----------------------------------------------------------------------------
// En-tête
// -----------------------------------------------------------------------------

echo "\n============================================================\n";
echo " CONTRÔLE DE COHÉRENCE DES FICHIERS\n";
echo "============================================================\n\n";

// -----------------------------------------------------------------------------
// Parcours des contrôles
// -----------------------------------------------------------------------------

foreach ($config['controles'] as $controle) {

    $nom               = $controle['nom'] ?? 'Contrôle sans nom';
    $table             = $controle['table'] ?? '';
    $colonneFichier    = $controle['colonne_fichier'] ?? '';
    $repertoire        = rtrim($controle['repertoire'] ?? '', '/\\');
    $colonnesAffichage = $controle['colonnes_affichage'] ?? [];
    $extensions        = $controle['extensions'] ?? [];

    if (empty($table) || empty($colonneFichier) || empty($repertoire)) {
        fwrite(STDERR, "Avertissement : Paramètres manquants pour le contrôle '{$nom}'. Ignoré.\n\n");
        continue;
    }

    $extensionsAutorisees = array_map(
        static fn(string $extension): string => strtolower(ltrim($extension, '.')),
        $extensions
    );

    // -------------------------------------------------------------------------
    // Affichage du contrôle
    // -------------------------------------------------------------------------

    echo "------------------------------------------------------------\n";
    echo " {$nom}\n";
    echo "------------------------------------------------------------\n\n";
    echo "Table      : {$table}\n";
    echo "Colonne    : {$colonneFichier}\n";
    echo "Répertoire : {$repertoire}\n";

    if ($extensionsAutorisees) {
        echo "Extensions : " . implode(', ', $extensionsAutorisees) . "\n";
    }

    echo "\n";

    // -------------------------------------------------------------------------
    // Récupération des fichiers référencés dans la base
    // -------------------------------------------------------------------------

    $colonnes = array_unique(
        array_merge([$colonneFichier], $colonnesAffichage)
    );

    $colonnesSql = implode(', ', array_map($echapperSql, $colonnes));
    $tableSql    = $echapperSql($table);
    $colonneSql  = $echapperSql($colonneFichier);

    $sql = "SELECT {$colonnesSql}
            FROM {$tableSql}
            WHERE {$colonneSql} IS NOT NULL
              AND {$colonneSql} <> ''";

    try {
        $lignes = $pdo->query($sql)->fetchAll();
    } catch (PDOException $e) {
        fwrite(STDERR, "Erreur SQL lors du contrôle '{$nom}' : " . $e->getMessage() . "\n\n");
        continue;
    }

    // -------------------------------------------------------------------------
    // Filtrage des résultats selon les extensions autorisées
    // -------------------------------------------------------------------------

    $fichiersReferences = [];
    $lignesValides      = [];

    foreach ($lignes as $ligne) {
        $fichier   = (string) $ligne[$colonneFichier];
        $extension = strtolower(pathinfo($fichier, PATHINFO_EXTENSION));

        if ($extensionsAutorisees && !in_array($extension, $extensionsAutorisees, true)) {
            continue;
        }

        $fichiersReferences[$fichier] = true;
        $lignesValides[]              = $ligne;
    }

    // -------------------------------------------------------------------------
    // Vérification des fichiers manquants sur le disque
    // -------------------------------------------------------------------------

    $nbManquants = 0;

    foreach ($lignesValides as $ligne) {
        $fichier = $ligne[$colonneFichier];
        $chemin  = $repertoire . DIRECTORY_SEPARATOR . $fichier;

        if (is_file($chemin)) {
            continue;
        }

        $nbManquants++;

        echo "FICHIER ABSENT\n";

        foreach ($colonnesAffichage as $colonne) {
            $valeur = $ligne[$colonne] ?? 'N/A';
            echo "  " . str_pad(ucfirst($colonne), 10, ' ', STR_PAD_RIGHT) . ": " . $valeur . "\n";
        }

        echo "  " . str_pad(ucfirst($colonneFichier), 10, ' ', STR_PAD_RIGHT) . ": " . $fichier . "\n\n";
    }

    // -------------------------------------------------------------------------
    // Recherche des fichiers orphelins sur le disque
    // -------------------------------------------------------------------------

    $fichiersOrphelins = [];

    if (!is_dir($repertoire)) {
        fwrite(STDERR, "Avertissement : Le répertoire n'existe pas ou n'est pas accessible : {$repertoire}\n\n");
    } else {
        $elements = scandir($repertoire);

        if ($elements !== false) {
            foreach ($elements as $element) {
                if ($element === '.' || $element === '..') {
                    continue;
                }

                $chemin = $repertoire . DIRECTORY_SEPARATOR . $element;

                if (!is_file($chemin)) {
                    continue;
                }

                $extension = strtolower(pathinfo($element, PATHINFO_EXTENSION));

                if ($extensionsAutorisees && !in_array($extension, $extensionsAutorisees, true)) {
                    continue;
                }

                if (isset($fichiersReferences[$element])) {
                    continue;
                }

                $fichiersOrphelins[] = $element;

                $orphelins[] = [
                    'controle'   => $nom,
                    'repertoire' => $repertoire,
                    'fichier'    => $element,
                ];
            }
        }
    }

    // -------------------------------------------------------------------------
    // Affichage du bilan du contrôle
    // -------------------------------------------------------------------------

    echo "Fichiers manquants : {$nbManquants}\n";
    echo "Fichiers orphelins : " . count($fichiersOrphelins) . "\n";

    foreach ($fichiersOrphelins as $fichier) {
        echo "  - {$fichier}\n";
    }

    echo "\n";
}

// -----------------------------------------------------------------------------
// Génération du fichier des orphelins
// -----------------------------------------------------------------------------

try {
    $jsonOrphelins = json_encode(
        $orphelins,
        JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    file_put_contents($fichierOrphelins, $jsonOrphelins);

    echo "------------------------------------------------------------\n";
    echo "BILAN GÉNÉRAL\n";
    echo "------------------------------------------------------------\n";
    echo "Fichier des orphelins        : {$fichierOrphelins}\n";
    echo "Total des fichiers orphelins : " . count($orphelins) . "\n\n";
} catch (JsonException $e) {
    fwrite(STDERR, "Erreur lors de l'écriture du fichier JSON des orphelins : " . $e->getMessage() . "\n");
    exit(1);
}

exit(0);