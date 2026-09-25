<?php
declare(strict_types=1);

/**
 * Template système : affiche un message (erreur, maintenance, accès refusé…)
 * dans la charte graphique du site (même header et footer que les pages normales).
 *
 * Variables attendues (injectées par InterfaceSystem::afficher()) :
 * @var string $titre         Titre de l'onglet
 * @var string $libelle       Libellé de l'en-tête de la carte (ex : "Avertissement")
 * @var string $type          Type CSS : erreur | avertissement | information | maintenance
 * @var string $message       Message à afficher (sera échappé)
 * @var string $lienRetour    URL du lien de retour
 * @var string $libelleRetour Texte du lien de retour
 */

$dossierRacine = dirname(__DIR__);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($titre) ?></title>
    <link rel="stylesheet" href="/composant/bootstrap/bootstrap.min.css">
    <script src="/composant/bootstrap/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .sys-card {
            max-width: 36rem;
            margin: 2rem auto;
            border: 1px solid #dee2e6;
            border-radius: .375rem .375rem 0 0;
            padding: 0;
        }

        .sys-card__header {
            padding: .75rem 1.25rem;
            font-weight: bold;
            font-size: 1.05rem;
            border-radius: .375rem .375rem 0 0;
        }

        .sys-card__header--avertissement { background-color: #ffc107; color: #000; }
        .sys-card__header--erreur        { background-color: #dc3545; color: #fff; }
        .sys-card__header--information   { background-color: #0dcaf0; color: #000; }
        .sys-card__header--maintenance   { background-color: #0D2366; color: #fff; }

        .sys-card__body {
            padding: 0.25rem 1.25rem;
            line-height: 1.6;
            text-align: justify;
            color: #998e91;
            white-space: pre-line;
            overflow-wrap: anywhere;
        }

        .sys-card__footer {
            padding: .75rem 1.25rem;
            text-align: center;
            border-top: 1px solid #dee2e6;
            background: #f9fafb;
            border-radius: 0 0 .375rem .375rem;
        }
    </style>
</head>
<body>

<?php require $dossierRacine . '/view/header.php'; ?>

<main>
    <div class="sys-card">

        <div class="sys-card__header sys-card__header--<?= htmlspecialchars($type) ?>">
            <?= htmlspecialchars($titre) ?>
        </div>
        <!-- doit être écrit sur une seule ligne, car les retours à la ligne sont pris en compte dans la classe sys-card__body : white-space -->
        <div class="sys-card__body"><?= htmlspecialchars($message) ?></div>

        <div class="sys-card__footer">
            <a href="#" onclick="history.back(); return false;">
                Revenir à la page précédente
            </a>
        </div>

    </div>
</main>

<?php require $dossierRacine . '/view/footer.php'; ?>

</body>
</html>
