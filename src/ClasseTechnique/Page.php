<?php
declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Classe Page
 *
 * ============================================================================
 * ROLE
 * ============================================================================
 *
 * Représente une page HTML à générer.
 *
 * Le contrôleur construit la page :
 *
 *       titre
 *       scripts JavaScript
 *       feuilles de style
 *       données destinées au JavaScript
 *       activation éventuelle de la protection CSRF
 *
 * Le template interface.php se charge ensuite de produire
 * le document HTML final.
 *
 *
 * ============================================================================
 * GESTION DU TOKEN CSRF
 * ============================================================================
 *
 * Une page n'a pas systématiquement besoin d'un token CSRF.
 *
 * Exemple :
 *
 * Page de consultation :
 *
 *      affichage d'une liste
 *      lecture d'informations
 *
 * Aucun token nécessaire.
 *
 *
 * Page avec modification :
 *
 *      ajout
 *      suppression
 *      modification AJAX
 *
 * Le contrôleur indique : $page->avecJeton();
 *
 * Le template pourra alors générer : <meta name="csrf-token" content="...">
 *
 * grâce à : Jeton::creer()
 *
 *
 * ============================================================================
 *
 * @author Guy Verghote
 * @version 2026.4
 * @date 12/08/2026
 */

class Page
{
    // propriétés décrivant une page
    private string $titre = '';

    /** @var list<string> */
    private array $scripts = [];

    /** @var list<string> */
    private array $styles = [];

    /** @var array<string,mixed> */
    private array $donnees = [];

    private bool $avecToken = false;

    /**
     * Définit le titre de la page.
     */
    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    /**
     * Retourne le titre de la page.
     */
    public function getTitre(): string
    {
        return $this->titre;
    }

    /**
     * Ajoute un fichier JavaScript.
     */
    public function addScript(string $url): self
    {
        if (!in_array($url, $this->scripts, true)) {
            $this->scripts[] = $url;
        }
        return $this;
    }

    /**
     * Retourne la liste des scripts JavaScript.
     * @return list<string>
     */
    public function getScripts(): array
    {
        return $this->scripts;
    }

    /**
     * Ajoute une feuille de style CSS.
     */
    public function addStyle(string $url): self
    {
        if (!in_array($url, $this->styles, true)) {
            $this->styles[] = $url;
        }
        return $this;
    }

    /**
     * Retourne la liste des feuilles de style.
     * @return list<string>
     */
    public function getStyles(): array
    {
        return $this->styles;
    }

    /**
     * Ajoute un composant complet.
     *
     * Convention :
     *
     * /composant/
     *      nom/
     *          nom.min.js
     *          nom.css
     * @throws UserException
     */
    public function addComposant(string $nom): self
    {
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $nom)) {
            throw new UserException("Nom de composant invalide.");
        }
        $this->addScript("/composant/$nom/$nom.min.js");
        $this->addStyle("/composant/$nom/$nom.css");
        return $this;
    }

    /**
     * Ajoute une donnée destinée au JavaScript.
     *
     * Exemple :
     *
     * $page->setDonnee('annonce', $annonce);
     *
     * deviendra disponible côté client.
     */
    public function setDonnee(string $nom, mixed $valeur): self
    {
        $this->donnees[$nom] = $valeur;
        return $this;
    }

    /**
     * Retourne les données destinées au JavaScript.
     * @return array<string,mixed>
     */
    public function getDonnees(): array
    {
        return $this->donnees;
    }

    /**
     * Indique que la page contient des actions nécessitant
     * une protection CSRF.
     *
     * Aucun token n'est créé ici.
     *
     * La création est reportée au template HTML.
     *
     * Cela évite de générer un token inutilement.
     */
    public function avecJeton(): self
    {
        $this->avecToken = true;
        return $this;
    }

    /**
     * Indique si la page doit fournir un token CSRF.
     */
    public function necessiteUnJeton(): bool
    {
        return $this->avecToken;
    }

    /**
     * Génère la page HTML finale.
     *
     * Le template interface.php reçoit l'objet Page
     * afin d'accéder à toutes les informations nécessaires.
     */
    public function afficher(): never
    {
        /*
         * L'objet page est volontairement transmis au template.
         *
         * Le template reste responsable :
         * - de la structure HTML ;
         * - des balises meta ;
         * - de l'injection éventuelle du token CSRF.
         */
        $page = $this;
        $fichier = DOSSIER_RACINE . '/view/interface.php';

        if (!is_file($fichier)) {
            throw new UserException("Le template interface.php est introuvable.");
        }

        require $fichier;
        exit;
    }
}