<?php
declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Classe abstraite représentant une colonne métier.
 * Elle encapsule la valeur, les règles de validation et les contraintes nécessaires aux opérations CRUD.
 *
 * @Author : Guy Verghote
 * @Version 2026.4
 * @Date : 22/09/2026
 */

abstract class Column
{
    public mixed $Value = null;
    public readonly bool $Required;
    public readonly bool $Insertable;
    public readonly bool $Updatable;

    protected string $validationMessage;

    public function __construct(bool $required = true, bool $insertable = true, bool $updatable = true)
    {
        $this->Required = $required;
        $this->Insertable = $insertable;
        $this->Updatable = $updatable;
        $this->Value = null;
        $this->validationMessage = '';
    }

    public function getValidationMessage(): string
    {
        return $this->validationMessage;
    }

    /**
     * Méthode de nettoyage/sanitisation.
     * À redéfinir dans les sous-classes si nécessaire (ex: ColumnText, ColumnTextarea).
     */
    public function sanitize(mixed $value): mixed
    {
        return $value;
    }

    /**
     * Vérifie que la valeur est renseignée quand la propriété Required est vraie.
     */
    public function checkValidity(): bool
    {
        // Nettoyage automatique avant tout contrôle
        if ($this->Value !== null) {
            $this->Value = $this->sanitize($this->Value);
        }

        if ($this->Required && ($this->Value === null || strlen(trim((string)$this->Value)) === 0)) {
            $this->validationMessage = "Veuillez renseigner ce champ.";
            return false;
        }

        return true;
    }
}
