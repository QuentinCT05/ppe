<?php

declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Construction des éléments HTML d'une page.
 *
 * Cette classe applique les conventions du framework.
 *
 * Elle recherche automatiquement :
 *
 *  Le header global ;
 *  le footer global ;
 *  le menu vertical ;
 *  le menu horizontal du module ;
 *  le template HTML de la page ;
 *  le CSS associé à la page ;
 *  le JavaScript associé à la page ;
 *  les ressources déclarées dans Page ;
 *  les données JavaScript ;
 *  le token CSRF.
 *
 * @author Guy Verghote
 * @version 2026.2
 * @date 25/09/2026
 */
class InterfaceHtml
{

    private Page $page;

    /**
     * Répertoire contenant la page courante.
     */
    private string $repertoirePage;

    /**
     * Nom de la page sans extension.
     */
    private string $nomPage;

    public function __construct(Page $page)
    {
        $this->page = $page;
        $this->repertoirePage = dirname($_SERVER['SCRIPT_FILENAME']);
        $this->nomPage = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
    }

    /**
     * Génère le contenu du head HTML.
     */
    public function head(): string
    {
        return implode("\n", [
            $this->csrf(),
            $this->bootstrap(),
            $this->menuVertical(),
            $this->menuHorizontal(),
            $this->styles(),
            $this->stylePage(),
            $this->scripts(),
            $this->scriptPage(),
            $this->donnees()
        ]);
    }

    /**
     * Génère l'entête commun.
     */
    public function header(): string
    {
        return $this->chargerFragment(DOSSIER_RACINE . '/view/header.php', ['page' => $this->page]);
    }

    /**
     * Génère le contenu spécifique de la page.
     */
    public function contenu(): string
    {
        return $this->chargerFragment($this->repertoirePage . '/' . $this->nomPage . '.html');
    }

    /**
     * Génère le pied de page commun.
     */
    public function footer(): string
    {
        return $this->chargerFragment(DOSSIER_RACINE . '/view/footer.php');
    }

    /**
     * Génération du token CSRF.
     */
    private function csrf(): string
    {
        if (!$this->page->necessiteUnJeton()) {
            return '';
        }

        return sprintf('<meta name="csrf-token" content="%s">', Jeton::creer());
    }

    /**
     * Ressources Bootstrap.
     */
    private function bootstrap(): string
    {
        return <<<HTML
            <link rel="stylesheet" href="/composant/bootstrap/bootstrap.min.css">
            <script src="/composant/bootstrap/bootstrap.bundle.min.js"></script>
            <link rel="stylesheet" href="/css/style.css">
HTML;
    }

    /**
     * Génération des feuilles de style.
     */
    private function styles(): string
    {
        $html = '';
        foreach ($this->page->getStyles() as $style) {
            $html .= sprintf('<link rel="stylesheet" href="%s">', $style);
        }
        return $html;
    }

    /**
     * Feuille de style CSS associée automatiquement à la page.
     */
    private function stylePage(): string
    {
        $fichier = $this->repertoirePage . '/' . $this->nomPage . '.css';
        if (!is_file($fichier)) {
            return '';
        }
        return sprintf('<link rel="stylesheet" href="%s?t=%s">', basename($fichier), filemtime($fichier));
    }

    /**
     * Génération des scripts déclarés dans Page.
     */
    private function scripts(): string
    {
        $html = '';
        foreach ($this->page->getScripts() as $script) {
            $html .= sprintf('<script src="%s"></script>', $script);
        }
        return $html;
    }

    /**
     * Script JavaScript associé automatiquement à la page.
     */
    private function scriptPage(): string
    {
        $fichier = $this->repertoirePage . '/' . $this->nomPage . '.js';
        if (!is_file($fichier)) {
            return '';
        }
        return sprintf('<script type="module" src="%s?t=%s"></script>', basename($fichier), filemtime($fichier));
    }

    /**
     * Données disponibles côté JavaScript.
     */
    private function donnees(): string
    {
        $html = '';
        foreach ($this->page->getDonnees() as $id => $valeur) {
            $json = ReponseJson::encoderPourHtml($valeur);
            $html .= sprintf('<script type="application/json" id="%s">%s</script>', $id, $json);
        }
        return $html;
    }

    /**
     * Génère le menu vertical global.
     */
    private function menuVertical(): string
    {
        $fichier = DOSSIER_RACINE . '/config/menuvertical.json';
        if (!is_file($fichier)) {
            return '';
        }
        $json = file_get_contents($fichier);
        if ($json === false) {
            return '';
        }
        return <<<HTML
            <script type="module">
            import { initialiserMenuVertical } from "/composant/menuvertical/menu.js";
            initialiserMenuVertical($json, 150);
            </script>
HTML;
    }

    /**
     * Génère le menu horizontal du module.
     */
    private function menuHorizontal(): string
    {
        $fichier = $this->repertoirePage . '/../config/menuhorizontal.json';
        if (!is_file($fichier)) {
            return '';
        }
        $json = file_get_contents($fichier);
        if ($json === false) {
            return '';
        }
        return <<<HTML
            <script type="module">
            import { initialiserMenuHorizontal } from "/composant/menuhorizontal/menu.js";
            initialiserMenuHorizontal($json);
            </script>
HTML;
    }

    /**
     * Charge un fragment PHP.
     */
    private function chargerFragment(string $fichier, array $variables = []): string
    {
        if (!is_file($fichier)) {
            return '';
        }
        extract($variables);
        ob_start();
        require $fichier;
        return ob_get_clean() ?: '';
    }
}