<?php

declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Classe ColumnTextarea : contrôle et nettoie une chaîne de caractères multiligne.
 *
 * @author Guy Verghote
 * @version 2026.3
 * @date 22/09/2026
 */
class ColumnTextarea extends Column
{
    // Encode le contenu HTML.
    public readonly bool $EncoderHtml;

    // Autorise le contenu HTML.
    public readonly bool $AcceptHtml;

    // Balises HTML autorisées.
    public readonly array $BalisesAutorisees;

    /**
     * Constructeur.
     */
    public function __construct(
        bool $required = true,
        bool $insertable = true,
        bool $updatable = true,
        bool $encoderHtml = false,
        bool $acceptHtml = true,
        array $balisesAutorisees = [
            '<br>',
            '<span>',
            '<b>',
            '<i>',
            '<strong>',
            '<ul>',
            '<li>',
            '<img>',
            '<a>',
            '<div>'
        ]
    ) {
        parent::__construct(
            required: $required,
            insertable: $insertable,
            updatable: $updatable
        );

        $this->EncoderHtml = $encoderHtml;
        $this->AcceptHtml = $acceptHtml;
        $this->BalisesAutorisees = $balisesAutorisees;
    }

    /**
     * Nettoie et filtre le contenu HTML avant validation.
     * Cette méthode est appelée automatiquement par parent::checkValidity().
     */
    public function sanitize(mixed $value): mixed
    {
        if ($value === null || !is_string($value)) {
            return $value;
        }

        // 1. Décodage des entités HTML (ex: transforme &lt;iframe&gt; en <iframe> réel)
        // pour que strip_tags et Std::nettoyerHtml puissent repérer la balise.
        $valeur = html_entity_decode((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // 2. Nettoyage générique du HTML (suppression des scripts, XSS, etc.)
        $valeur = Std::nettoyerHtml($valeur);

        // 3. Encodage complet si demandé (ex: pour du texte brut)
        if ($this->EncoderHtml) {
            return htmlspecialchars(
                $valeur,
                ENT_QUOTES,
                'UTF-8'
            );
        }

        // 4. Suppression des balises non autorisées si le HTML est restreint
        if (!$this->AcceptHtml) {
            return strip_tags(
                $valeur,
                $this->BalisesAutorisees
            );
        }

        return $valeur;
    }

    /**
     * Vérifie la validité du contenu multiligne.
     */
    public function checkValidity(): bool
    {
        // Appel du parent qui exécute $this->sanitize($this->Value) et vérifie Required
        if (!parent::checkValidity()) {
            return false;
        }

        // La valeur a déjà été nettoyée et assignée par parent::checkValidity()
        return true;
    }
}