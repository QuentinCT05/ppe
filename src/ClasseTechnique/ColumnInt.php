<?php
declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Classe ColumnInt : contrôle un entier.
 *
 * @author Guy Verghote
 * @version 2026.2
 * @date 12/08/2026
 */
class ColumnInt extends Column
{
    // Valeur minimale autorisée.
    public readonly ?int $Min;

    // Valeur maximale autorisée.
    public readonly ?int $Max;

    /**
     * Constructeur.
     */
    public function __construct(bool $required = true, bool $insertable = true, bool $updatable = true, ?int $min = null, ?int $max = null)
    {
        parent::__construct(
            required: $required,
            insertable: $insertable,
            updatable: $updatable
        );

        $this->Min = $min;
        $this->Max = $max;
    }

    /**
     * Vérifie la validité de l'entier.
     */
    public function checkValidity(): bool
    {
        if (!parent::checkValidity()) {
            return false;
        }

        // Une valeur facultative peut rester vide.
        if ($this->Value === null || $this->Value === '') {
            return true;
        }

        $valeur = (string)$this->Value;

        // Vérification du format entier.
        if (!preg_match('/^-?\d+$/', $valeur)) {
            $this->validationMessage = "La valeur doit être un entier.";
            return false;
        }

        $valeur = (int)$valeur;

        // Vérification de la valeur minimale.
        if ($this->Min !== null && $valeur < $this->Min) {
            $this->validationMessage = "La valeur ne peut pas être inférieure à {$this->Min}.";
            return false;
        }

        // Vérification de la valeur maximale.
        if ($this->Max !== null && $valeur > $this->Max) {
            $this->validationMessage = "La valeur ne peut pas être supérieure à {$this->Max}.";
            return false;
        }

        // On conserve la valeur normalisée sous forme d'entier.
        $this->Value = $valeur;

        return true;
    }
}
