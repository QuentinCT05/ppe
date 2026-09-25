<?php

declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Classe ColumnList : contrôle qu'une valeur appartient à une liste.
 *
 * @author Guy Verghote
 * @version 2026.2
 * @date 12/08/2026
 */
class ColumnList extends Column
{
    // Valeurs autorisées.
    private readonly array $Values;

    // Gestion de la casse.
    private readonly TextCase $Casse;

    /**
     * Constructeur.
     */
    public function __construct(
        bool $required = true,
        bool $insertable = true,
        bool $updatable = true,
        array $values = [],
        TextCase $casse = TextCase::None
    ) {
        parent::__construct(
            required: $required,
            insertable: $insertable,
            updatable: $updatable
        );

        $this->Values = $values;
        $this->Casse = $casse;
    }

    /**
     * Retourne les valeurs autorisées.
     *
     * @return array
     */
    public function getValues(): array
    {
        return $this->Values;
    }

    /**
     * Vérifie que la valeur appartient à la liste autorisée.
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

        // Transformation éventuelle de la casse.
        $this->Value = match ($this->Casse) {
            TextCase::Upper => strtoupper((string)$this->Value),
            TextCase::Lower => strtolower((string)$this->Value),
            TextCase::None,
            TextCase::Word,
            TextCase::First => $this->Value,
        };

        // Vérification de l'appartenance à la liste.
        if (!in_array($this->Value, $this->Values, true)) {
            $this->validationMessage =
                "Veuillez saisir une des valeurs autorisées.";
            return false;
        }

        return true;
    }
}