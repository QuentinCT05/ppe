<?php
declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Classe ColumnDate : contrôle une date au format aaaa-mm-jj.
 *
 * @Author : Guy Verghote
 * @Version 2026.2
 * @Date : 13/08/2026
 */
class ColumnDate extends Column
{
    // Date minimale autorisée.
    public readonly ?string $Min;

    //  Date maximale autorisée.
    public readonly ?string $Max;

    // Constructeur.
    public function __construct(bool $required = true, bool $insertable = true, bool $updatable = true, ?string $min = null, ?string $max = null)
    {
        parent::__construct(required: $required, insertable: $insertable, updatable: $updatable);
        $this->Min = $min;
        $this->Max = $max;
    }

    // Redéfinition de la méthode checkValidity
    public function checkValidity(): bool
    {
        // Vérification des règles communes aux colonnes.
        if (!parent::checkValidity()) {
            return false;
        }

        // Une date facultative peut rester vide.
        if ($this->Value === null || $this->Value === '') {
            return true;
        }

        $valeur = (string)$this->Value;

        // Vérification du format AAAA-MM-JJ.
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $valeur, $date)) {
            $this->validationMessage = "Le format de la date est invalide.";
            return false;
        }

        $annee = (int)$date[1];
        $mois = (int)$date[2];
        $jour = (int)$date[3];


        // Vérification de l'existence réelle de la date.
        if ($annee <= 1900 || !checkdate($mois, $jour, $annee)) {
            $this->validationMessage = "Le format de la date est invalide.";
            return false;
        }

        // Date minimale.
        if ($this->Min !== null && $valeur < $this->Min) {
            $this->validationMessage = "La date doit être égale ou postérieure au " . self::decoderDate($this->Min);
            return false;
        }

        // Date maximale.
        if ($this->Max !== null && $valeur > $this->Max) {
            $this->validationMessage = "La date doit être égale ou antérieure au " . self::decoderDate($this->Max);
            return false;
        }
        return true;
    }

    /**
     * Convertit une date AAAA-MM-JJ au format JJ/MM/AAAA.
     */
    private static function decoderDate(string $date): string
    {
        $tab = explode('-', substr($date, 0, 10));
        return sprintf('%02d/%02d/%04d', (int)$tab[2], (int)$tab[1], (int)$tab[0]);
    }
}