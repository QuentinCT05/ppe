<?php
declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Classe Input : Classe abstraite pour le contrôle des données
 *
 * @Author : Guy Verghote
 * @Version : 2026.2
 * @date    12/08/2026
 */
abstract class Input
{
    protected mixed $value = null;
    protected bool $required = true;
    protected string $validationMessage;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->value = null;
        $this->required = true;
        $this->validationMessage = '';
    }

    /**
     * Accesseur
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Définit la valeur de la propriété value
     * @param mixed $value
     * @return void
     */
    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    /**
     * Accesseur sur required
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * Définit la valeur de la propriété required
     * @param bool $required
     * @return void
     */
    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }



    /**
     * Accesseur sur le message d'erreur
     * @return string
     */
    public function getValidationMessage(): string
    {
        return $this->validationMessage;
    }

    /**
     *
     * Accesseur en écriture sur le message d'erreur
     *
     * @param string $message
     * @return void
     */
    public function setValidationMessage(string $message): void
    {
        $this->validationMessage = $message;
    }

    /**
     * Vérifie que la valeur est renseignée quant la propriété Requite est vraie
     * @return bool
     */
    public function checkValidity(): bool
    {
        // Attention : la valeur du champ peut contenir une chaîne vide ou contenir uniquement des espaces
        if ($this->required && ($this->value === null || strlen(trim((string)$this->value)) === 0)) {
            $this->validationMessage = "Veuillez renseigner ce champ ";
            return false;
        }
        return true;
    }
}
