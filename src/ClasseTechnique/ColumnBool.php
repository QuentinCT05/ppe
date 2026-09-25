<?php
declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Classe ColumnBool : contrôle une valeur booléenne.
 *
 * @Author : Guy Verghote
 * @Version 2026.2
 * @Date : 12/08/2026
 */
class ColumnBool extends Column
{
    // Constructeur.
    public function __construct(bool $required = true, bool $insertable = true, bool $updatable = true)
    {
        parent::__construct(required: $required, insertable: $insertable, updatable: $updatable);
    }

    // Redéfinition de la méthode checkValidity.
    public function checkValidity(): bool
    {
        if (!parent::checkValidity()) {
            return false;
        }

        if ($this->Value !== null) {

            // Conversion des valeurs issues d'un formulaire HTML.
            if ($this->Value === '1' || $this->Value === 1 || $this->Value === true) {
                $this->Value = true;
            } elseif ($this->Value === '0' || $this->Value === 0 || $this->Value === false) {
                $this->Value = false;
            }elseif ($this->Value === 'true') {
                $this->Value = true;
            } elseif ($this->Value === 'false') {
                $this->Value = false;
            } else {
                $this->validationMessage = "La valeur doit être vraie ou fausse.";
                return false;
            }
        }

        return true;
    }
}