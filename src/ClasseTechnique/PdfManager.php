<?php

declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Classe PdfManager
 *
 * Gestionnaire spécialisé des fichiers PDF.
 *
 * Cette classe définit uniquement les caractéristiques
 * propres aux documents PDF :
 *
 *  Extensions autorisées ;
 *  types MIME autorisés ;
 *  taille maximale ;
 *  suppression éventuelle des accents.
 *
 * La gestion commune est assurée par FileManager :
 *
 *  Configuration de InputFile ;
 *  validation ;
 *  copie physique ;
 *  suppression ;
 *  consultation du répertoire.
 *
 * Aucun traitement spécifique n'est nécessaire pour les PDF.
 *
 * @author Guy Verghote
 * @version 2026.2
 * @date : 12/08/2026
 */
class PdfManager extends FileManager
{
    // Extensions autorisées.
    protected array $lesExtensions = ['pdf'];

    // Types MIME autorisés.
    protected array $lesTypes = ['application/pdf'];

    // Taille maximale autorisée (en octets).
    protected int $maxSize = 0;

    // Indique si les accents doivent être supprimés du nom du fichier.
    protected bool $sansAccent = true;

    /**
     *
     * Redéfinition du constructeur pour permettre la configuration de la taille maximale, du renommage et de la suppression des accents.
     * @param string $repertoire
     * @param int $maxSize
     * @param bool $sansAccent
     * @throws UserException
     */
    public function __construct(string $repertoire, int $maxSize = 0,  bool $sansAccent = true )
    {
        parent::__construct($repertoire);
        $this->maxSize = $maxSize;
        $this->sansAccent = $sansAccent;
    }

    /**
     * Configuration complémentaire de InputFile.
     *
     * @param InputFile $inputFile
     */
    protected function configurerInputFile(InputFile $inputFile): void
    {
        $inputFile->setMaxSize($this->maxSize);
        $inputFile->setSansAccent($this->sansAccent);
    }
}