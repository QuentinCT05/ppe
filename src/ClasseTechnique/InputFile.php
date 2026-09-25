<?php

declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Gestion d'un fichier téléversé.
 *
 * Vérifie un fichier reçu avant son traitement.
 *
 * @author Guy Verghote
 * @version 2026.2
 * @date    31/07/2026
 */
class InputFile extends Input
{
    // Fichier reçu.
    protected array $file;
    // Validation effectuée.
    protected bool $valide = false;
    // Transformation de casse.
    protected string $casse = '';
    // Suppression des accents.
    protected bool $sansAccent = true;
    // Extensions autorisées.
    protected array $lesExtensions = [];
    // Types MIME autorisés.
    protected array $lesTypes = [];
    // Taille maximale.
    protected int $maxSize = 0;

    /**
     * Constructeur.
     */
    public function __construct(array $fichier)
    {
        parent::__construct();

        if (!isset($fichier['name'], $fichier['tmp_name'], $fichier['error'], $fichier['size'])) {
            throw new UserException("Le fichier doit contenir les clés : name, tmp_name, error et size.");
        }

        $this->file = $fichier;
        $this->setValue((string)$fichier['name']);
    }

    /**
     * Définit les extensions autorisées.
     */
    public function setLesExtensions(array $extensions): void
    {
        $this->lesExtensions = array_map('strtolower', $extensions);
    }

    /**
     * Définit les types MIME autorisés.
     */
    public function setLesTypes(array $types): void
    {
        $this->lesTypes = array_map('strtolower', $types);
    }

    /**
     * Définit la taille maximale.
     */
    public function setMaxSize(int $maxSize): void
    {
        $this->maxSize = max(0, $maxSize);
    }

    /**
     * Active ou non la suppression des accents.
     */
    public function setSansAccent(bool $sansAccent): void
    {
        $this->sansAccent = $sansAccent;
    }

    /**
     * Définit la casse.
     *
     * @param string $casse '' pour aucune, 'U' pour majuscules, 'L' pour minuscules
     *
     * @throws UserException si la casse n'est pas valide
     */
    public function setCasse(string $casse): void
    {
        if (!in_array($casse, ['', 'U', 'L'], true)) {
            throw new UserException("La casse doit être vide, U ou L.");
        }

        $this->casse = $casse;
    }

    /**
     * Retourne le fichier reçu.
     */
    public function getFile(): array
    {
        return $this->file;
    }

    /**
     * Retourne le nom d'origine.
     */
    public function getName(): string
    {
        return (string)$this->file['name'];
    }


    /**
     * Retourne le fichier temporaire.
     */
    public function getTmpName(): string
    {
        return (string)$this->file['tmp_name'];
    }

    /**
     * Retourne la taille.
     */
    public function getSize(): int
    {
        return (int)$this->file['size'];
    }

    /**
     * Retourne le code erreur.
     */
    public function getError(): int
    {
        return (int)$this->file['error'];
    }

    /**
     * Retourne l'extension.
     */
    public function getExtension(): string
    {
        return strtolower(pathinfo($this->getName(), PATHINFO_EXTENSION));
    }

    /**
     * Retourne le type MIME.
     */
    public function getMimeType(): string
    {
        if (!is_file($this->getTmpName())) {
            return '';
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        if ($finfo === false) {
            return '';
        }

        $mime = finfo_file($finfo, $this->getTmpName());
        finfo_close($finfo);

        return $mime === false ? '' : strtolower($mime);
    }

    /**
     * Indique si le fichier est valide.
     */
    public function isValide(): bool
    {
        return $this->valide;
    }

    /**
     * Vérifie le fichier.
     */
    public function checkValidity(): bool
    {
        $this->valide = false;

        if (!$this->verifierTeleversement()) {
            return false;
        }

        if (!$this->verifierTaille()) {
            return false;
        }

        if (!$this->verifierExtension()) {
            return false;
        }

        if (!$this->verifierTypeMime()) {
            return false;
        }

        if (!$this->validationSpecifique()) {
            return false;
        }

        if (!$this->preparerNomFichier()) {
            return false;
        }

        $this->valide = true;

        return true;
    }

    /**
     * Validation complémentaire.
     */
    protected function validationSpecifique(): bool
    {
        return true;
    }

    /**
     * Vérifie le téléversement.
     */
    protected function verifierTeleversement(): bool
    {
        if ($this->getError() === UPLOAD_ERR_NO_FILE) {
            if ($this->isRequired()) {
                $this->validationMessage = "Aucun fichier n'a été téléversé.";
                return false;
            }

            return true;
        }

        if ($this->getError() !== UPLOAD_ERR_OK) {
            $this->validationMessage = $this->getMessageErreurUpload();
            return false;
        }

        if (!is_file($this->getTmpName())) {
            $this->validationMessage = "Le fichier temporaire est introuvable.";
            return false;
        }

        return true;
    }

    /**
     * Message erreur upload.
     */
    protected function getMessageErreurUpload(): string
    {
        return match ($this->getError()) {
            UPLOAD_ERR_INI_SIZE => "La taille du fichier dépasse la limite PHP.",
            UPLOAD_ERR_FORM_SIZE => "La taille du fichier dépasse la limite du formulaire.",
            UPLOAD_ERR_PARTIAL => "Le fichier n'a été que partiellement téléversé.",
            UPLOAD_ERR_NO_FILE => "Aucun fichier n'a été téléversé.",
            UPLOAD_ERR_NO_TMP_DIR => "Le dossier temporaire PHP est absent.",
            UPLOAD_ERR_CANT_WRITE => "Impossible d'écrire le fichier sur le disque.",
            UPLOAD_ERR_EXTENSION => "Une extension PHP a interrompu le téléversement.",
            default => "Une erreur inconnue est survenue lors du téléversement."
        };
    }

    /**
     * Vérifie la taille.
     */
    protected function verifierTaille(): bool
    {
        if ($this->maxSize === 0 || $this->getSize() <= $this->maxSize) {
            return true;
        }

        $this->validationMessage = "La taille du fichier (" . $this->getSize() . " octets) dépasse la taille autorisée (" . $this->maxSize . " octets).";

        return false;
    }

    /**
     * Vérifie l'extension.
     */
    protected function verifierExtension(): bool
    {
        $extension = $this->getExtension();

        if (in_array($extension, $this->lesExtensions, true)) {
            return true;
        }

        $this->validationMessage = "L'extension .$extension n'est pas autorisée.";

        return false;
    }

    /**
     * Vérifie le type MIME.
     */
    protected function verifierTypeMime(): bool
    {
        $mime = $this->getMimeType();

        if (in_array($mime, $this->lesTypes, true)) {
            return true;
        }

        $this->validationMessage = "Le type MIME '$mime' n'est pas autorisé.";

        return false;
    }

    /**
     * Prépare le nom du fichier.
     */
    protected function preparerNomFichier(): bool
    {
        $nom = $this->getName();
        $nom = $this->appliquerCasse($nom);

        if ($this->sansAccent) {
            $nom = $this->supprimerAccents($nom);
        }

        $nom = $this->nettoyerNom($nom);

        if ($nom === '') {
            $this->validationMessage = "Le nom du fichier est invalide.";
            return false;
        }

        $this->setValue($nom);

        return true;
    }

    /**
     * Applique la casse.
     */
    protected function appliquerCasse(string $nom): string
    {
        return match ($this->casse) {
            'U' => strtoupper($nom),
            'L' => strtolower($nom),
            default => $nom
        };
    }

    /**
     * Supprime les accents.
     *
     * @param string $nom Le nom à traiter
     * @return string Le nom sans accents
     *
     */
    protected function supprimerAccents(string $nom): string
    {
        if (class_exists(\Transliterator::class)) {
            $resultat = transliterator_transliterate('Any-Latin; Latin-ASCII', $nom);

            if ($resultat !== false) {
                return $resultat;
            }
        }

        $resultat = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nom);

        return $resultat === false ? $nom : $resultat;
    }

    /**
     * Nettoie le nom.
     */
    protected function nettoyerNom(string $nom): string
    {
        $nom = preg_replace('/[^a-zA-Z0-9._ -]/', '', $nom);
        $nom = preg_replace('/\s+/', ' ', $nom ?? '');

        if ($nom === '' || str_starts_with($nom, '.')) {
            return '';
        }

        return trim($nom);
    }
}