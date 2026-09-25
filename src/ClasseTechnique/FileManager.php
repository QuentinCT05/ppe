<?php

declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Gestion du stockage physique des fichiers.
 *
 * Les classes filles définissent :
 *
 * Les extensions autorisées ;
 * Les types MIME autorisés ;
 * Les contrôles spécifiques.
 *
 * @author Guy Verghote
 * @version 2026.1
 * @date    12/06/2026
 */
abstract class FileManager
{
    // Répertoire physique de stockage.
    protected string $repertoire;
    // Extensions autorisées.
    protected array $lesExtensions = [];
    // Types MIME autorisés.
    protected array $lesTypes = [];

    /**
     * Constructeur.
     *
     * @throws UserException
     */
    public function __construct(string $repertoire)
    {
        if ($this->lesExtensions === []) {
            throw new UserException("Aucune extension autorisée n'a été définie.");
        }
        if ($this->lesTypes === []) {
            throw new UserException("Aucun type MIME autorisé n'a été défini.");
        }

        if (!is_dir($repertoire)) {
            throw new UserException("Le répertoire de stockage n'existe pas.");
        }

        $this->lesExtensions = array_map('strtolower', $this->lesExtensions);
        $this->lesTypes = array_map('strtolower', $this->lesTypes);
        $this->repertoire = $repertoire;
    }

    /**
     * Retourne le répertoire physique.
     */
    public function getRepertoire(): string
    {
        return $this->repertoire;
    }

    // ==========================================================
    // Consultation des fichiers
    // ==========================================================

    /**
     * Retourne les fichiers présents.
     */
    public function getLesFichiers(): array
    {
        $contenu = scandir($this->repertoire);

        if ($contenu === false) {
            return [];
        }

        $liste = [];
        foreach ($contenu as $fichier) {
            if ($fichier === '.' || $fichier === '..') {
                continue;
            }

            $extension = strtolower(pathinfo($fichier, PATHINFO_EXTENSION));

            if (in_array($extension, $this->lesExtensions, true)) {
                $liste[] = $fichier;
            }
        }
        sort($liste);
        return $liste;
    }

    /**
     * Indique si un fichier existe.
     */
    public function existe(string $nomFichier): bool
    {
        if (!$this->verifierNomFichier($nomFichier)) {
            return false;
        }

        return is_file(
            $this->repertoire
            . DIRECTORY_SEPARATOR
            . $nomFichier
        );
    }

    // ==========================================================
    // Gestion des fichiers
    // ==========================================================

    /**
     * Ajoute un fichier.
     */
    public function ajouter(InputFile $inputFile, bool $rename = false): bool
    {
        if (!$this->preparerInputFile($inputFile, $rename)) {
            return false;
        }

        if (!$rename && $this->existe((string)$inputFile->getValue())) {
            $inputFile->setValidationMessage("Le fichier '" . $inputFile->getValue() . "' existe déjà.");
            return false;
        }

        return $this->copierFichier($inputFile);
    }

    /**
     * Cette méthode est redéfinie dans la classe ImageMager qui peut intégrer sa version spécifique pour traiter le redimensionnement
     *
     * @param InputFile $file
     * @return bool
     */
    protected function copierFichier(InputFile $file): bool
    {
        return $this->copier($file);
    }

    /**
     * Remplace un fichier.
     */
    public function remplacer(string $ancienNom, InputFile $inputFile): bool
    {
        if (!$this->verifierNomFichier($ancienNom)) {
            return false;
        }

        if (!$this->existe($ancienNom)) {
            return false;
        }

        if (!$this->preparerInputFile($inputFile, false)) {
            return false;
        }

        return $this->remplacerPhysiquement($inputFile, $ancienNom);
    }

    /**
     * Supprime un fichier.
     */
    public function supprimer(string $nomFichier): bool
    {
        if (!$this->verifierNomFichier($nomFichier)) {
            return false;
        }

        $fichier = $this->repertoire . DIRECTORY_SEPARATOR . $nomFichier;
        if (!is_file($fichier)) {
            return true;
        }
        return unlink($fichier);
    }

    /**
     * Enregistre un fichier.
     *
     * Si aucun ancien nom n'est fourni, le fichier est ajouté.
     * Sinon le fichier existant est remplacé en conservant son nom.
     *
     * @param string|null $ancienNom
     * @param InputFile $inputFile
     *
     * @return bool
     */
    public function enregistrer(?string $ancienNom, InputFile $inputFile): bool
    {
        if ($ancienNom === null || $ancienNom === '') {
            return $this->ajouter($inputFile, false);
        }

        return $this->remplacer($ancienNom, $inputFile);
    }

    // ==========================================================
    // Préparation du fichier
    // ==========================================================

    /**
     * Prépare un fichier.
     */
    protected function preparerInputFile(
        InputFile $inputFile,
        bool      $rename
    ): bool
    {
        $inputFile->setLesExtensions($this->lesExtensions);
        $inputFile->setLesTypes($this->lesTypes);

        $this->configurerInputFile($inputFile);

        if (!$inputFile->checkValidity()) {
            return false;
        }

        if ($rename) {
            $this->renommerSiNecessaire($inputFile);
        }

        return true;
    }

    /**
     * Configuration spécifique.
     */
    protected function configurerInputFile(InputFile $inputFile): void
    {
    }

    /**
     * Vérifie un nom de fichier.
     */
    protected function verifierNomFichier(string $nom): bool
    {
        if ($nom === '') {
            return false;
        }

        if (basename($nom) !== $nom) {
            return false;
        }

        $extension = strtolower(pathinfo($nom, PATHINFO_EXTENSION));
        return in_array($extension, $this->lesExtensions, true);
    }

    /**
     * Renomme si le fichier existe déjà.
     */
    protected function renommerSiNecessaire(InputFile $inputFile): void
    {
        $nom = (string)$inputFile->getValue();
        if (!$this->existe($nom)) {
            return;
        }
        $nomBase = pathinfo($nom, PATHINFO_FILENAME);
        $extension = pathinfo($nom, PATHINFO_EXTENSION);

        $i = 1;
        do {
            if ($extension === '') {
                $nouveauNom = $nomBase . '(' . $i . ')';
            } else {
                $nouveauNom = $nomBase . '(' . $i . ').' . $extension;
            }

            $i++;
        } while ($this->existe($nouveauNom));

        $inputFile->setValue($nouveauNom);
    }

    // ==========================================================
    // Copie physique
    // ==========================================================

    /**
     * Copie un fichier dans le stockage.
     */
    protected function copier(InputFile $file): bool
    {
        if (!is_file($file->getTmpName())) {
            return false;
        }

        $nom = (string)$file->getValue();

        if (!$this->verifierNomFichier($nom)) {
            return false;
        }

        $destination = $this->repertoire
            . DIRECTORY_SEPARATOR
            . $nom;

        if (is_file($destination)) {
            return false;
        }

        if (!copy($file->getTmpName(), $destination)) {
            return false;
        }

        return unlink($file->getTmpName());
    }

    /**
     * Remplace physiquement un fichier.
     */
    protected function remplacerPhysiquement(InputFile $file, string $nom): bool
    {
        if (!is_file($file->getTmpName())) {
            return false;
        }

        if (!$this->verifierNomFichier($nom)) {
            return false;
        }

        $destination = $this->repertoire . DIRECTORY_SEPARATOR . $nom;
        $temporaire = tempnam($this->repertoire, 'remplacement_');

        if ($temporaire === false) {
            return false;
        }

        if (!copy($file->getTmpName(), $temporaire)) {
            unlink($temporaire);
            return false;
        }

        if (!rename($temporaire, $destination)) {
            unlink($temporaire);
            return false;
        }

        return unlink($file->getTmpName());
    }
}