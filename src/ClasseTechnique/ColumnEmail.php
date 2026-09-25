<?php
declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Classe ColumnEmail : contrôle une adresse email.
 *
 * @author Guy Verghote
 * @version 2026.2
 * @date 12/08/2026
 *
 * Utilisation : new ColumnEmail(required: true, maxLength: 100)
 * Le DNS n'est pas vérifié.
 *
 * new ColumnEmail(required: true, maxLength: 100, verifierDomaine: true)
 * Le DNS est vérifié.
 *
 */
class ColumnEmail extends Column
{
    // Longueur maximale autorisée.
    public readonly ?int $MaxLength;

    // Indique si le domaine de l'adresse doit être vérifié par DNS.
    public readonly bool $VerifierDomaine;

    /**
     * Constructeur.
     */
    public function __construct(bool $required = true, bool $insertable = true, bool $updatable = true, ?int $maxLength = null, bool $verifierDomaine = false)
    {
        parent::__construct(
            required: $required,
            insertable: $insertable,
            updatable: $updatable
        );

        $this->MaxLength = $maxLength;
        $this->VerifierDomaine = $verifierDomaine;
    }

    /**
     * Vérifie la validité de l'adresse email.
     */
    public function checkValidity(): bool
    {
        if (!parent::checkValidity()) {
            return false;
        }

        // Une adresse facultative peut rester vide.
        if ($this->Value === null || $this->Value === '') {
            return true;
        }

        $valeur = trim((string)$this->Value);

        // Vérification du format de l'adresse.
        if (!filter_var($valeur, FILTER_VALIDATE_EMAIL)) {
            $this->validationMessage = "Le format de l'adresse électronique est invalide.";
            return false;
        }

        // Vérification de la longueur maximale.
        if (
            $this->MaxLength !== null && strlen($valeur) > $this->MaxLength
        ) {
            $this->validationMessage = "L'adresse électronique ne doit pas dépasser " . $this->MaxLength . " caractères.";
            return false;
        }

        // Vérification optionnelle de l'existence du domaine.
        if ($this->VerifierDomaine) {
            $domaine = substr(strrchr($valeur, '@'), 1);
            if (!checkdnsrr($domaine, 'MX')) {
                $this->validationMessage = "Le domaine de l'adresse électronique n'existe pas.";
                return false;
            }
        }

        // On conserve la valeur nettoyée.
        $this->Value = $valeur;
        return true;
    }
}
