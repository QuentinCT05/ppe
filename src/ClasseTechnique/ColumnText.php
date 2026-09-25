<?php

declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Gestion de la casse.
 */
enum TextCase
{
    case None;
    case Upper;
    case Lower;
    case Word;
    case First;
}

/**
 * Classe ColumnText : contrôle et nettoie une chaîne de caractères.
 *
 * @author Guy Verghote
 * @version 2026.3
 * @date 22/09/2026
 */
class ColumnText extends Column
{
    /**
     * Expression régulière de validation.
     */
    public readonly ?string $Pattern;

    /**
     * Longueur minimale autorisée.
     */
    public readonly ?int $MinLength;

    /**
     * Longueur maximale autorisée.
     */
    public readonly ?int $MaxLength;

    /**
     * Transformation de casse.
     */
    public readonly TextCase $Casse;

    /**
     * Indique si les accents doivent être supprimés.
     */
    public readonly bool $SupprimerAccent;

    /**
     * Indique si les espaces multiples doivent être remplacés
     * par un espace unique.
     */
    public readonly bool $SupprimerEspaceSuperflu;

    /**
     * Constructeur.
     */
    public function __construct(
        bool $required = true,
        bool $insertable = true,
        bool $updatable = true,
        ?string $pattern = null,
        ?int $minLength = null,
        ?int $maxLength = null,
        TextCase $casse = TextCase::None,
        bool $supprimerAccent = false,
        bool $supprimerEspaceSuperflu = false
    ) {
        parent::__construct(
            required: $required,
            insertable: $insertable,
            updatable: $updatable
        );

        $this->Pattern = $pattern;
        $this->MinLength = $minLength;
        $this->MaxLength = $maxLength;
        $this->Casse = $casse;
        $this->SupprimerAccent = $supprimerAccent;
        $this->SupprimerEspaceSuperflu = $supprimerEspaceSuperflu;
    }

    /**
     * Nettoie et normalise la chaîne avant validation.
     * Cette méthode est appelée automatiquement par parent::checkValidity().
     */
    public function sanitize(mixed $value): mixed
    {
        if ($value === null || !is_string($value)) {
            return $value;
        }

        // Nettoyage métier initial (titres, caractères invisibles, etc.)
        $valeur = Std::nettoyerTitre($value);

        // Suppression éventuelle des accents
        if ($this->SupprimerAccent) {
            $valeur = $this->sansAccent($valeur);
        }

        // Réduction des espaces multiples à un seul espace
        if ($this->SupprimerEspaceSuperflu) {
            $valeur = preg_replace('/\s+/u', ' ', $valeur) ?? $valeur;
        }

        // Transformation de la casse
        return match ($this->Casse) {
            TextCase::Upper => mb_strtoupper($valeur, 'UTF-8'),
            TextCase::Lower => mb_strtolower($valeur, 'UTF-8'),
            TextCase::Word => mb_convert_case(
                mb_strtolower($valeur, 'UTF-8'),
                MB_CASE_TITLE,
                'UTF-8'
            ),
            TextCase::First => $this->mettrePremiereLettreEnMajuscule($valeur),
            TextCase::None => $valeur,
        };
    }

    /**
     * Contrôle la validité de la valeur.
     */
    public function checkValidity(): bool
    {
        // Appel du parent qui exécute $this->sanitize($this->Value) et vérifie Required
        if (!parent::checkValidity()) {
            return false;
        }

        // Une valeur facultative peut rester vide
        if ($this->Value === null || $this->Value === '') {
            return true;
        }

        // $this->Value est déjà nettoyée et normalisée
        $valeur = (string)$this->Value;

        // Vérification de l'expression régulière
        if ($this->Pattern !== null) {
            $pattern = $this->getPcrePattern($this->Pattern);

            if ($pattern === '' || @preg_match($pattern, '') === false) {
                $this->validationMessage = 'Le format de validation est invalide.';
                return false;
            }

            if (@preg_match($pattern, $valeur) !== 1) {
                $this->validationMessage = "La valeur transmise n'est pas valide.";
                return false;
            }
        }

        // Vérification de la longueur minimale
        $nbCar = mb_strlen($valeur, 'UTF-8');

        if ($this->MinLength !== null && $nbCar < $this->MinLength) {
            $this->validationMessage =
                "Veuillez allonger ce texte pour qu'il comporte au moins "
                . $this->MinLength
                . " caractères. Il en compte actuellement "
                . $nbCar
                . '.';

            return false;
        }

        // Vérification de la longueur maximale
        if ($this->MaxLength !== null && $nbCar > $this->MaxLength) {
            $this->validationMessage =
                "Veuillez réduire ce texte afin de ne pas dépasser "
                . $this->MaxLength
                . ' caractères.';

            return false;
        }

        return true;
    }

    /**
     * Retourne une expression PCRE valide.
     */
    private function getPcrePattern(string $pattern): string
    {
        $pattern = trim($pattern);

        if ($pattern === '') {
            return '';
        }

        if (preg_match('/^([^\\w\\s\\\\]).+\\1[imsxADSUXJu]*$/', $pattern) === 1) {
            return $pattern;
        }

        return '/' . str_replace('/', '\/', $pattern) . '/u';
    }

    /**
     * Supprime les accents.
     */
    private function sansAccent(string $valeur): string
    {
        if (function_exists('transliterator_transliterate')) {
            return transliterator_transliterate(
                'Any-Latin; Latin-ASCII',
                $valeur
            );
        }

        return iconv(
            'UTF-8',
            'ASCII//TRANSLIT//IGNORE',
            $valeur
        ) ?: $valeur;
    }

    /**
     * Met la première lettre en majuscule et le reste en minuscules.
     */
    private function mettrePremiereLettreEnMajuscule(string $valeur): string
    {
        if ($valeur === '') {
            return '';
        }

        $premiere = mb_substr($valeur, 0, 1, 'UTF-8');
        $reste = mb_substr($valeur, 1, null, 'UTF-8');

        return mb_strtoupper($premiere, 'UTF-8')
            . mb_strtolower($reste, 'UTF-8');
    }
}