<?php
declare(strict_types=1);

/** @var \ClasseTechnique\Page $page */

use ClasseTechnique\InterfaceHtml;

$html = new InterfaceHtml($page);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $page->getTitre() ?></title>
    <?= $html->head() ?>
</head>
<body>
<?= $html->header() ?>
<main>
    <?= $html->contenu() ?>
</main>
<?= $html->footer() ?>
</body>
</html>