<?php

declare(strict_types=1);

namespace ClasseTechnique;

use Gumlet\ImageResize;
use Gumlet\ImageResizeException;

/**
 * Classe ImageManager
 *
 * Gestionnaire spécialisé des fichiers images.
 *
 * Définit :
 *
 *  Extensions autorisées ;
 *  types MIME autorisés ;
 *  taille maximale ;
 *  suppression des accents ;
 *  redimensionnement éventuel.
 *
 * La gestion commune des fichiers est assurée par FileManager.
 *
 * @author Guy Verghote
 * @version 2026.2
 * @date    12/08/2026
 */
class ImageManager extends FileManager
{
    // Extensions autorisées.
    protected array $lesExtensions = ['jpg', 'jpeg', 'png', 'webp', 'avif'];

    // Types MIME autorisés.
    protected array $lesTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/avif', 'image/heif'];

    // Taille maximale autorisée.
    protected int $maxSize = 0;

    // Suppression des accents.
    protected bool $sansAccent = true;

    // Redimensionnement automatique.
    protected bool $redimensionner = false;

    // Largeur maximale.
    protected int $width = 0;

    // Hauteur maximale.
    protected int $height = 0;

    /**
     * Constructeur.
     *
     * @param string $repertoire Répertoire de stockage des images.
     * @param int $maxSize Taille maximale autorisée en octets.
     * @param bool $sansAccent Supprime les accents des noms de fichiers.
     * @param bool $redimensionner Active le redimensionnement automatique.
     * @param int $width Largeur maximale.
     * @param int $height Hauteur maximale.
     *
     * @throws UserException Si le répertoire de stockage est invalide.
     */
    public function __construct(string $repertoire, int $maxSize = 0, bool $sansAccent = true, bool $redimensionner = false, int $width = 0, int $height = 0)
    {
        parent::__construct($repertoire);
        $this->maxSize = $maxSize;
        $this->sansAccent = $sansAccent;
        $this->redimensionner = $redimensionner;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Configuration complémentaire de InputFile.
     *
     * @param InputFile $inputFile L'objet InputFile à configurer.
     *
     */
    protected function configurerInputFile(InputFile $inputFile): void
    {
        $inputFile->setMaxSize($this->maxSize);
        $inputFile->setSansAccent($this->sansAccent);
    }

    /**
     * Copie un fichier.
     *
     * Si le fichier est une image et que le redimensionnement est activé,
     * la copie se fait avec redimensionnement éventuel.
     *
     * @param InputFile $file Le fichier à copier.
     * @return bool true si la copie a réussi, false sinon.
     */
    protected function copierFichier(InputFile $file): bool
    {
        if ($file instanceof InputFileImg && $this->redimensionner) {
            return $this->copierImage($file);
        }
        return parent::copierFichier($file);
    }

    /**
     * Copie une image avec redimensionnement éventuel.
     *
     * @param InputFileImg $file L'image à copier.
     * @return bool true si la copie a réussi, false sinon.
     */
    protected function copierImage(InputFileImg $file): bool
    {
        if (!$this->redimensionner || ($this->width === 0 && $this->height === 0)) {
            return $this->copier($file);
        }
        $destination = $this->repertoire . DIRECTORY_SEPARATOR . $file->getValue();
        try {
            $image = new ImageResize($file->getTmpName());

            if ($this->width > 0 && $this->height === 0) {
                if ($image->getSourceWidth() > $this->width) {
                    $image->resizeToWidth($this->width);
                }
            } elseif ($this->height > 0 && $this->width === 0) {
                if ($image->getSourceHeight() > $this->height) {
                    $image->resizeToHeight($this->height);
                }
            } elseif ($this->width > 0 && $this->height > 0) {
                $image->resizeToBestFit($this->width, $this->height);
            }

            if (!$image->save($destination)) {
                return false;
            }
            unlink($file->getTmpName());
            return true;
        } catch (ImageResizeException $e) {
            $file->setValidationMessage(
                "Erreur lors du redimensionnement : " . $e->getMessage()
            );
            return false;
        }
    }
}