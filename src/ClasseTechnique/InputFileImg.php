<?php

declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Gestion d'un fichier image téléversé.
 *
 * Ajoute les contrôles spécifiques aux images.
 * @author Guy Verghote
 * @version 2026.2
 * @date    12/08/2026
 */
class InputFileImg extends InputFile
{
    // Largeur minimale autorisée.
    protected int $minWidth = 0;

    // Largeur maximale autorisée.
    protected int $maxWidth = 0;

    // Hauteur minimale autorisée.
    protected int $minHeight = 0;

    // Hauteur maximale autorisée.
    protected int $maxHeight = 0;


    /**
     * Définit les dimensions minimales et maximales.
     *
     * @param int $minWidth
     * @param int $maxWidth
     * @param int $minHeight
     * @param int $maxHeight
     */
    public function setDimensions(int $minWidth = 0, int $maxWidth = 0, int $minHeight = 0, int $maxHeight = 0): void
    {
        $this->minWidth = max(0, $minWidth);
        $this->maxWidth = max(0, $maxWidth);
        $this->minHeight = max(0, $minHeight);
        $this->maxHeight = max(0, $maxHeight);
    }


    /**
     * Validation spécifique aux images.
     *
     * Appelée automatiquement par InputFile::checkValidity()
     */
    protected function validationSpecifique(): bool
    {
        $infos = getimagesize($this->getTmpName());

        if ($infos === false) {
            $this->validationMessage = "Le fichier n'est pas une image valide.";
            return false;
        }

        $largeur = $infos[0];
        $hauteur = $infos[1];

        if ($this->minWidth > 0 && $largeur < $this->minWidth) {
            $this->validationMessage = "La largeur minimale de l'image est de {$this->minWidth}px.";
            return false;
        }

        if ($this->maxWidth > 0 && $largeur > $this->maxWidth) {
            $this->validationMessage = "La largeur maximale de l'image est de {$this->maxWidth}px.";
            return false;
        }

        if ($this->minHeight > 0 && $hauteur < $this->minHeight) {
            $this->validationMessage = "La hauteur minimale de l'image est de {$this->minHeight}px.";
            return false;
        }

        if ($this->maxHeight > 0 && $hauteur > $this->maxHeight) {
            $this->validationMessage = "La hauteur maximale de l'image est de {$this->maxHeight}px.";
            return false;
        }

        return true;
    }

    /**
     * Retourne les dimensions de l'image.
     *
     * @return array<string,int>|null
     */
    public function getDimensions(): ?array
    {
        if (!is_file($this->getTmpName())) {
            return null;
        }

        $infos = getimagesize($this->getTmpName());

        if ($infos === false) {
            return null;
        }

        return [
            'largeur' => $infos[0],
            'hauteur' => $infos[1]
        ];
    }
}