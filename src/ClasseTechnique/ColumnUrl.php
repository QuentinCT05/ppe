<?php

declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Classe ColumnUrl : contrôle une URL.
 *
 * @author Guy Verghote
 * @version 2026.2
 * @date 12/08/2026
 */
class ColumnUrl extends Column
{
    // Vérifie que l'URL correspond à une ressource existante.
    public readonly bool $VerifierExistence;

    /**
     * Constructeur.
     */
    public function __construct(
        bool $required = true,
        bool $insertable = true,
        bool $updatable = true,
        bool $verifierExistence = false
    )
    {
        parent::__construct(required: $required, insertable: $insertable, updatable: $updatable);

        $this->VerifierExistence = $verifierExistence;
    }

    /**
     * Vérifie la validité de l'URL.
     */
    public function checkValidity(): bool
    {
        if (!parent::checkValidity()) {
            return false;
        }

        // Une URL facultative peut rester vide.
        if ($this->Value === null || $this->Value === '') {
            return true;
        }

        $valeur = trim((string)$this->Value);

        // Vérification du format de l'URL.
        if (!filter_var($valeur, FILTER_VALIDATE_URL)) {
            $this->validationMessage = "L'URL n'est pas valide.";
            return false;
        }

        // Vérification optionnelle de l'existence de la ressource.
        if ($this->VerifierExistence && !$this->verifyUrlExistence($valeur)) {
            $this->validationMessage = "L'URL ne correspond pas à une ressource accessible.";
            return false;
        }

        // Conservation de la valeur normalisée.
        $this->Value = $valeur;

        return true;
    }

    /**
     * Vérifie que l'URL correspond à une ressource accessible.
     */
    private function verifyUrlExistence(string $url): bool
    {
        $headers = @get_headers($url);

        if ($headers === false || !isset($headers[0])) {
            return false;
        }

        return preg_match('/^HTTP\/\S+\s+2\d{2}\b/', $headers[0]) === 1;
    }
}