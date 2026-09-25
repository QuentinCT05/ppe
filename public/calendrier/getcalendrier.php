<?php
declare(strict_types=1);

use ClasseTechnique\Requete;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// ------------------------------------
// Paramètres
// ------------------------------------
$zone = Requete::getNullableString('zone') ?? 'Somme';
$saison = Requete::getNullableInt('saison') ?? (int)date('Y');

$cacheFile = __DIR__ . "/cache/calendrier_{$zone}_{$saison}.html";
$cacheTime = 3600; // 1h

// ------------------------------------
// Cache
// ------------------------------------
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
    echo file_get_contents($cacheFile);
    exit;
}

// ------------------------------------
// Construction URL FFA
// ------------------------------------
$url = "https://bases.athle.fr/asp.net/liste.aspx?"
    . "frmpostback=true"
    . "&frmbase=calendrier"
    . "&frmmode=1"
    . "&frmespace=0"
    . "&frmtype1=Hors+Stade";

if ($zone === 'Somme') {
    $url .= "&frmdepartement=080";
} elseif ($zone === 'HDF') {
    $url .= "&frmligue=H-F";
}

$url .= "&frmsaisonffa=" . $saison;

// ------------------------------------
// Appel cURL
// ------------------------------------
$ch = curl_init($url);

curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/140.0 Safari/537.36",
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_CAINFO =>  'j:VirtualHostSlam/cacert.pem',
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
]);

$html = curl_exec($ch);

if ($html === false) {
    echo '<pre>';
    echo "URL : " . $url . "\n";
    echo "cURL errno : " . curl_errno($ch) . "\n";
    echo "cURL error : " . curl_error($ch) . "\n";
    echo "HTTP code : " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n";
    echo '</pre>';

    curl_close($ch);
    exit;
}

curl_close($ch);

if (!$html) {
    echo "<p>Erreur de chargement du calendrier.</p>";
    exit;
}

// ------------------------------------
// Extraction simple du tableau FFA
// ------------------------------------
libxml_use_internal_errors(true);

$dom = new DOMDocument();
$dom->loadHTML($html);

$xpath = new DOMXPath($dom);

// ⚠️ On récupère les tableaux (FFA en a généralement 1 principal)
$table = $xpath->query("//table")->item(0);

if (!$table) {
    echo "<p>Aucune donnée disponible.</p>";
    exit;
}

// ------------------------------------
// Sortie HTML
// ------------------------------------
ob_start();

echo '<div class="container-fluid px-0">';

foreach ($table->getElementsByTagName('tr') as $tr) {

    $td = $tr->getElementsByTagName('td');

    if ($td->length === 0) {
        continue;
    }

    // Texte de la première cellule
    $texte = trim($td->item(0)->textContent);

    // Ignore l'en-tête et le nombre de résultats
    if (str_starts_with($texte, 'Résultats de votre recherche')
            || preg_match('/^\d+\s+résultats/i', $texte)) {
        continue;
    }

    // ----------------------------
    // Titre du mois
    // ----------------------------
    if ($td->length === 1 && preg_match('/^[A-Za-zÉÈÊÀÂÎÔÛÙÇ].+\d{4}$/u', $texte)) {

        echo '<h2 class="mt-4 mb-3">'
                . htmlspecialchars($texte)
                . '</h2>';

        continue;
    }

    // ----------------------------
    // Ligne compétition
    // ----------------------------
    if ($td->length < 8) {
        continue;
    }

    $date = trim($td->item(0)->textContent);
    $nom = trim($td->item(1)->textContent);

    // Ville = premier nœud texte uniquement
    $ville = '';

    foreach ($td->item(2)->childNodes as $node) {
        if ($node instanceof DOMText) {
            $ville = trim($node->nodeValue);
            break;
        }
    }

    $niveau = trim($td->item(4)->textContent);

    // lien fiche
    $fiche = '';
    $a = $td->item(6)->getElementsByTagName('a')->item(0);

    // valeur de $a /competitions/149846281849887855286855710855455855

    if ($a) {
        $fiche = 'https://www.athle.fr' . $a->getAttribute('href');
    }

    // lien résultats
    $resultats = '';
    $a = $td->item(7)->getElementsByTagName('a')->item(0);

    // valeur de $a : /bases/liste.aspx?frmbase=resultats&frmmode=1&frmespace=0&frmcompetition=29610  2

    if ($a) {
        $resultats = 'https://www.athle.fr' . $a->getAttribute('href');
    }

    ?>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <h5 class="card-title">
                🏃 <?= htmlspecialchars($nom) ?>
            </h5>
            <p>📅 <?= htmlspecialchars($date) ?></p>
            <p>📍 <?= htmlspecialchars($ville) ?></p>
            <p>🏅 <?= htmlspecialchars($niveau) ?></p>

            <div class="d-grid d-md-flex gap-2">
                <?php if ($fiche) { ?>
                    <a class="btn btn-primary"
                       href="<?= htmlspecialchars($fiche) ?>"
                       target="_blank">
                        📄 Fiche
                    </a>
                <?php } ?>
                <?php if ($resultats) { ?>
                    <a class="btn btn-outline-primary"
                       href="<?= htmlspecialchars($resultats) ?>"
                       target="_blank">
                        🏁 Résultats
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php
}

echo '</div>';

$content = ob_get_clean();

// ------------------------------------
// Cache write
// ------------------------------------
file_put_contents($cacheFile, $content);

// Output
echo $content;
