<?php
declare(strict_types=1);

namespace ClasseTechnique;

use Exception;
use PDO;

/**
 * Classe abstraite représentant une table de la base de données.
 *
 * Cette classe centralise toutes les opérations communes aux classes métier :
 *
 *  Connexion à la base de données ;
 *  gestion des erreurs de validation ;
 *  gestion des objets Column associés aux colonnes ;
 *  validation générique des données ;
 *  contrôle des contraintes d'unicité ;
 *  insertion ;
 *  modification ;
 *  suppression.
 *
 * Les classes filles doivent uniquement définir :
 *
 *  Le nom de la table ;
 *  la clé primaire ;
 *  les colonnes et leurs règles de validation ;
 *  les colonnes autorisées pour les opérations CRUD ;
 *  éventuellement les règles métier spécifiques.
 *
 * Exemple :
 *
 * Une classe Categorie n'a pas à gérer :
 *  les requêtes INSERT ;
 *  les requêtes UPDATE ;
 *  les contrôles d'existence ;
 *  les erreurs techniques.
 *
 * Elle ne doit gérer que les règles propres aux catégories :
 *  âge minimum inférieur à l'âge maximum ;
 *  règles particulières métier.
 *
 * @author Guy Verghote
 * @version 2026.5
 * @date : 22/09/2026
 */
abstract class Table
{
    /**
     * Connexion PDO utilisée par toutes les opérations SQL.
     */
    private PDO $db;

    // Nom de la table SQL. : initialiser pour éviter une erreur PHP lors du contrôle avec validateConfiguration
    // si le développeur oublie de définir sa valeur dans la méthode configure
    protected string $table = '';

    // Nom de la colonne servant de clé primaire.
    protected string $primaryKey = '';

    // Liste des objets Column représentant les colonnes.
    private array $columns = [];

    // Tableau associatif des Erreurs rencontrées lors des contrôles.
    protected array $errors = [];

    // * Dernier identifiant généré après insertion. Utile lorsque la clé primaire est auto-incrémentée.
    protected int|string|false $lastInsertId = false;

    //  Constructeur commun.
    //  La classe fille doit appeler ma méthode "parent::__construct()" avant de définir ses colonnes.
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->configure();
        $this->validateConfiguration();
    }

    private function validateConfiguration(): void
    {
        if ($this->table === '') {
            throw new Exception("Le nom de la table n'est pas défini.");
        }

        if ($this->primaryKey === '') {
            throw new Exception("La clé primaire n'est pas définie.");
        }

        if (empty($this->columns)) {
            throw new Exception("Aucune colonne métier n'est définie pour la table '{$this->table}'.");
        }
    }

    // ==========================================================
    // Accesseurs
    // ==========================================================

    /**
     * Retourne les erreurs détectées.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Retourne le dernier identifiant créé.
     *
     * Retourne false si aucune insertion
     * n'a encore été réalisée.
     */
    public function getLastInsertId(): int|string|false
    {
        return $this->lastInsertId;
    }

    /**
     * Retourne le nom de la clé primaire de la table.
     */
    public function getPrimaryKeyName(): string
    {
        return $this->primaryKey;
    }

    /**
     * Retourne un objet Column associé à une colonne.
     */
    public function getColumn(string $column): Column
    {
        if (!isset($this->columns[$column])) {
            throw new Exception("La colonne '$column' n'existe pas dans la table '{$this->table}'."
            );
        }
        return $this->columns[$column];
    }

    /**
     * Ajoute une erreur dans le tableau des erreurs.
     *
     * Le champ peut être :
     *
     *  Le nom d'une colonne ou global lorsqu'une erreur concerne plusieurs champs.
     *
     * Exemple : $this→addErreur('global','Un coureur identique existe déjà.');
     */
    protected function addError(string $field, string $message): void
    {
        $this->errors[$field] = $message;
    }

    /*
     * Permet à un service d'ajouter directement une erreur
     */
    public function addServiceError(string $field, string $message): void
    {
        $this->addError($field, $message);
    }

    /**
     * Ajoute une colonne à l'objet.
     *
     * @param string $name
     * @param Column $column
     * @return void
     * @throws Exception
     */
    protected function addColumn(string $name, Column $column): void
    {
        if (isset($this->columns[$name])) {
            throw new Exception("La colonne $name existe déjà.");
        }

        $this->columns[$name] = $column;
    }

    /**
     * Réinitialise l'objet avant chaque opération.
     *
     * Cette méthode supprime les anciennes erreurs et remet toutes les valeurs Column à null.
     *
     * Cela évite qu'une validation précédente influence une nouvelle opération.
     */
    protected function reset(): void
    {
        $this->errors = [];
        foreach ($this->columns as $input) {
            $input->Value = null;
        }
    }

    /**
     * Retourne la valeur d'une colonne.
     *
     * Permet à une classe fille de récupérer une valeur validée.
     *
     * @param string $column Nom de la colonne
     * @return mixed
     */
    protected function getValue(string $column): mixed
    {
        return $this->columns[$column]->Value;
    }

    /**
     * Définit une valeur dans un objet Column.
     *
     * Utilisée notamment par les classes filles
     * lorsqu'elles doivent préparer une donnée
     * avant une opération SQL.
     *
     * @param string $column Nom de la colonne
     * @param mixed $value Valeur à définir
     */
    protected function setValue(string $column, mixed $value): void
    {
        if (isset($this->columns[$column])) {
            $this->columns[$column]->Value = $value;
        }
    }

    // ==========================================================
    // Méthodes de contrôle génériques
    // ==========================================================

    /**
     * Vérifie si un enregistrement existe dans la table.
     *
     * Cette méthode utilise la clé primaire définie par la classe fille.
     *
     * @param mixed $id Valeur recherchée
     *
     * @return bool
     */
    protected function exists(mixed $id): bool
    {
        $sql = "select 1
                from {$this->table}
                where {$this->primaryKey} = :id";
        $cmd = $this->db->prepare($sql);
        $cmd->bindValue('id', $id);
        $cmd->execute();
        $exists = (bool)$cmd->fetch();
        $cmd->closeCursor();
        return $exists;
    }

    /**
     * Retourne un enregistrement complet à partir de sa clé primaire.
     *
     * @param mixed $id
     * @return array|null
     */
    protected function getRowById(mixed $id): ?array
    {
        $sql = "select *
                from {$this->table}
                where {$this->primaryKey} = :id";

        $cmd = $this->db->prepare($sql);
        $cmd->bindValue('id', $id);
        $cmd->execute();
        $row = $cmd->fetch(PDO::FETCH_ASSOC);
        $cmd->closeCursor();
        return $row === false ? null : $row;
    }

    /**
     * Vérifie que les données reçues sont conformes.
     *
     * Les contrôles réalisés sont :
     *
     * — Absence de colonnes interdites ;
     * — présence des colonnes obligatoires ;
     * — validation des valeurs à l'aide des objets Column.
     *
     * @param array $data Données transmises à contrôler.
     * @param array $allowedColumns Colonnes autorisées.
     * @param array $requiredColumns Colonnes obligatoires parmis les colonnes autorisées.
     *
     * @return bool
     */
    protected function checkColumns(array $data, array $allowedColumns, array $requiredColumns): bool
    {
        $ok = true;

        // Recherche des colonnes interdites : Différence au niveau des clés entre les données transmises et les données autorisées
        $forbiddenColumns = array_diff(array_keys($data), $allowedColumns);

        // génération des messages d'erreur pour chaque colonne interdite
        foreach ($forbiddenColumns as $column) {
            $this->addError($column, "Cette colonne n'est pas autorisée.");
            $ok = false;
        }

        // Recherche des colonnes obligatoires manquantes : différence entre les données du tableau $requireColums et les clés du tableau des données transmises
        $missingColumns = array_diff($requiredColumns, array_keys($data));

        // génération des messages d'erreurs pour chaque colonne obligatoire manquante
        foreach ($missingColumns as $column) {
            $this->addError($column, "Cette colonne est obligatoire.");
            $ok = false;
        }

        // Si des erreurs ont été trouvées, il est inutile de poursuivre la validation
        if (!$ok) {
            return false;
        }

        // Validation des valeurs.
        foreach ($data as $column => $value) {
            if (!isset($this->columns[$column])) {
                continue;
            }

            $input = $this->columns[$column];

            // 1. Affectation de la valeur brute
            $this->setValue($column, $value);

            // 2. Gestion spécifique des champs optionnels vides
            if (!$input->Required && is_string($value) && trim($value) === '') {
                $this->addError($column, "Pour un champ optionnel, transmettez null et non une chaîne vide.");
                $ok = false;
                continue;
            }

            // 3. checkValidity() exécute sanitize() et valide la valeur nettoyée
            if (!$input->checkValidity()) {
                $this->addError($column, $input->getValidationMessage());
                $ok = false;
            }
        }
        return $ok;
    }

    /**
     * Alimente les données ne provenant pas d'une saisie utilisateur
     * PAr exemple : utilisateur connecté, date courante
     *
     * Cette méthode est redéfinie dans les classes métiers
     */
    protected function populateData(): void
    {
        // Implémentation par défaut : aucune donnée calculée à préparer
    }



    // ==========================================================
    // Points d'extension pour les classes filles
    // ==========================================================

    /**
     * Définit les colonnes utilisables lors d'un ajout.
     *
     * Par défaut toutes les colonnes sont autorisées.
     *
     * Une classe dérivée peut redéfinir cette méthode.
     *
     * Exemple :
     *
     * Une table avec une clé auto-incrémentée
     * peut exclure la clé primaire.
     */
    protected function getInsertColumns(): array
    {
        $columns = [];
        foreach ($this->columns as $name => $column) {
            if ($column->Insertable) {
                $columns[] = $name;
            }
        }
        return $columns;
    }

    /**
     *
     * Retourne les colonnes obligatoires parmi les colonnes autorisées.
     * Il s'agit des colonnes dont la valeur est requise pour l'ajout d'un enregistrement : Propriété Require à true
     *
     * @param array $allowedColumns
     * @return array
     */
    protected function getRequiredColumns(array $allowedColumns): array
    {
        $requiredColumns = [];

        foreach ($allowedColumns as $column) {
            if ($this->columns[$column]->Required) {
                $requiredColumns[] = $column;
            }
        }

        return $requiredColumns;
    }

    /**
     * Définit les colonnes utilisables lors d'une modification.
     *
     * Par défaut toutes les colonnes sont modifiables sauf la colonne primary key
     *
     * Une classe dérivée peut limiter les modifications.
     *
     */
    protected function getUpdateColumns(): array
    {
        $columns = [];
        foreach ($this->columns as $name => $column) {
            if ($name !== $this->primaryKey && $column->Updatable) {
                $columns[] = $name;
            }
        }
        return $columns;
    }

    /**
     * Traitement spécifique avant une opération de mise à jour : ajout ou modification.
     *
     * Cette méthode est appelée automatiquement par add().
     *
     * La classe dérivée peut la redéfinir afin d'effectuer
     * des contrôles métier propres à la table.
     *
     * Exemple : Vérifier que la valeur de la variable ageMin est inférieure à ageMax.
     *
     * @return bool
     */
    protected function beforeChange(): bool
    {
        return true;
    }

    /**
     * Traitement spécifique avant un ajout.
     *
     * Cette méthode est appelée automatiquement par add().
     *
     * La classe dérivée peut la redéfinir afin d'effectuer
     * des contrôles métier propres à la table.
     *
     */
    protected function beforeInsert(): bool
    {
        return true;
    }

    /**
     * Traitement spécifique avant une modification.
     *
     * Cette méthode est appelée automatiquement par modify().
     *
     * La classe dérivée peut la redéfinir afin d'effectuer
     * des contrôles métier propres à la table.
     *
     */
    /** @noinspection PhpUnusedParameter */
    protected function beforeUpdate(mixed $id): bool
    {
        return true;
    }

    /**
     * Traitement spécifique avant une suppression.
     *
     * Cette méthode est appelée automatiquement par delete().
     *
     * La classe dérivée peut empêcher la suppression
     * d'un enregistrement.
     *
     * Exemple :
     *
     * Une catégorie ne peut pas être supprimée
     * si des coureurs lui sont encore associés.
     */
    /** @noinspection PhpUnusedParameter */
    protected function beforeDelete(mixed $id): bool
    {
        return true;
    }

    /**
     * Traitement spécifique après un ajout.
     *
     * Cette méthode est appelée automatiquement par add().
     *
     * La classe dérivée peut la redéfinir afin d'effectuer
     * des actions spécifiques après l'ajout d'un enregistrement.
     *
     */
    /** @noinspection PhpUnusedParameter */
    protected function afterInsert(mixed $id): bool
    {
        return true;
    }

    /**
     * @param mixed $id
     * @return bool
     */
    /** @noinspection PhpUnusedParameter */
    protected function afterUpdate(mixed $id): bool
    {
        return true;
    }

    /**
     * @param mixed $id
     * @return bool
     */
    /** @noinspection PhpUnusedParameter */
    protected function afterDelete(mixed $id): bool
    {
        return true;
    }

    // ==========================================================
    // Opérations CRUD publiques
    // ==========================================================

    /**
     * Ajoute un enregistrement.
     *
     * Déroulement :
     *
     * 1) Réinitialisation
     * 2) Contrôle générique
     * 3) Contrôle métier
     * 4) Insertion SQL
     *
     *
     * La classe fille n'a donc plus besoin
     * de préparer sa requête INSERT.
     */
    public function add(array $data): bool
    {
        $this->reset();

        // récupération des colonnes utilisables en ajout
        $allowedColumns = $this->getInsertColumns();

        // remplir automatiquement des données manquantes
        $this->populateData();

        // Validation des données reçues
        if (!$this->checkColumns($data, $allowedColumns, $this->getRequiredColumns($allowedColumns))) {
            return false;
        }

        // Contrôle métier commun aux opérations d'écriture
        if (!$this->beforeChange()) {
            return false;
        }

        // Contrôle métier spécifique
        if (!$this->beforeInsert()) {
            return false;
        }

        // Insertion
        $this->insert();

        // on récupère l'identifiant qui peut être utilisé si une action à réaliser après l'ajout est définie dans la classe métier
        $insertedId = $this->getInsertedId();
        if (!$this->afterInsert($insertedId)) {
            return false;
        }

        return true;
    }

    protected function getInsertedId(): mixed
    {
        if (isset($this->columns[$this->primaryKey])) {
            return $this->columns[$this->primaryKey]->Value ?? $this->lastInsertId;
        }

        return $this->lastInsertId;
    }


    /**
     * Modifie un enregistrement.
     *
     * Déroulement :
     *
     * 1) Réinitialisation
     * 2) Vérification existence
     * 3) Chargement des valeurs courantes si nécessaire
     * 4) Contrôle générique
     * 5) Contrôle métier
     * 6) Mise à jour SQL
     */
    public function modify(mixed $id, array $data): bool
    {
        // initialisation
        $this->reset();

        $updateColumns = $this->getUpdateColumns();

        if (empty($data)) {
            $this->addError('global', "Aucune colonne à modifier.");
            return false;
        }

        // préparation des données calculées
        $this->populateData();

        // as t-on reçu toutes les colonnes modifiables ?
        $missingColumns = array_diff($updateColumns, array_keys($data));
        $isPartialUpdate = !empty($missingColumns);

        // En cas de mise à jour partielle, rechargement des valeurs courantes
        // pour conserver la cohérence des contrôles métier et d'unicité.
        if ($isPartialUpdate) {
            $row = $this->getRowById($id);
            if ($row === null) {
                $this->addError($this->primaryKey, "Cet enregistrement n'existe pas.");
                return false;
            }

            foreach ($this->columns as $name => $input) {
                if (array_key_exists($name, $row)) {
                    $this->setValue($name, $row[$name]);
                }
            }
        } else {
            // Vérification simple de l'existence de l'enregistrement à modifier
            if (!$this->exists($id)) {
                $this->addError($this->primaryKey, "Cet enregistrement n'existe pas.");
                return false;
            }
        }

        // Validation des données reçues.
        if (!$this->checkColumns($data, $updateColumns, $isPartialUpdate ? [] : $updateColumns)) {
            return false;
        }

        // Contrôle métier commun aux opérations d'écriture
        if (!$this->beforeChange()) {
            return false;
        }

        // contrôle métier spécifique
        if (!$this->beforeUpdate($id)) {
            return false;
        }

        // mise à jour
        $this->update($id);

        return $this->afterUpdate($id);
    }

    /**
     * Supprime un enregistrement.
     *
     * Déroulement :
     *
     * 1) Réinitialisation des erreurs
     * 2) Vérification de l'existence
     * 3) Contrôle métier éventuel
     * 4) Suppression SQL
     *
     *
     * La classe dérivée peut intervenir avant suppression
     * en redéfinissant beforeDelete().
     *
     * Exemple :
     *
     * Une catégorie ne peut pas être supprimée
     * si des coureurs y sont associés.
     */
    public function delete(mixed $id): bool
    {
        $this->reset();

        // Vérification de l'existence de l'enregistrement à supprimer
        if (!$this->exists($id)) {
            $this->addError($this->primaryKey, "Cet enregistrement n'existe pas.");
            return false;
        }

        // Contrôle métier spécifique.
        if (!$this->beforeDelete($id)) {
            return false;
        }

        // Suppression
        $sql = "delete from {$this->table} where {$this->primaryKey} = :id";
        $cmd = $this->db->prepare($sql);
        $cmd->bindValue('id', $id);
        $cmd->execute();

        return $this->afterDelete($id);
    }

    /**
     * Insère un nouvel enregistrement.
     *
     * La requête est construite automatiquement avec les colonnes autorisées qui ont reçu une valeur lors de la validation.
     */
    protected function insert(): void
    {
        $columns = [];
        $parameters = [];

        // récupération des colonnes utilisables lors d'un ajout
        $insertColumns = $this->getInsertColumns();

        // génération de la liste des colonnes et des paramètres pour la requête SQL
        // insert into nom table (....) values (....)
        foreach ($insertColumns as $column) {
            $input = $this->columns[$column];
            if ($input->Value !== null) {
                $columns[] = $column;
                $parameters[] = ":$column";
            }
        }

        // test de sécurité même si peu probable : aucune colonne
        if (empty($columns)) {
            throw new Exception("Aucune colonne à insérer.");
        }

        $sql = sprintf(
            "insert into %s (%s) values (%s)",
            $this->table,
            implode(',', $columns),
            implode(',', $parameters)
        );

        $cmd = $this->db->prepare($sql);

        // Génération des méthodes bindValue associées à chaque 'values'
        foreach ($insertColumns as $column) {
            $input = $this->columns[$column];
            if ($input->Value !== null) {
                $cmd->bindValue($column, $input->Value);
            }
        }
        $cmd->execute();
        $this->lastInsertId = $this->db->lastInsertId();
    }

    /**
     * Met à jour un enregistrement.
     *
     * Toutes les colonnes modifiables sont intégrées dans UPDATE.
     *
     * Les champs optionnels doivent être transmis à null (et non en chaîne vide).
     */
    protected function update(mixed $id): void
    {
        $updates = [];

        $updateColumns = $this->getUpdateColumns();

        foreach ($updateColumns as $column) {
            $updates[] = "$column = :$column";
        }

        if (empty($updates)) {
            return;
        }

        $sql = "UPDATE {$this->table} SET "
            . implode(',', $updates)
            . " WHERE {$this->primaryKey} = :id";

        $cmd = $this->db->prepare($sql);

        foreach ($updateColumns as $column) {
            $cmd->bindValue($column, $this->columns[$column]->Value);
        }
        $cmd->bindValue('id', $id);
        $cmd->execute();
    }

    // ==========================================================
    // Méthodes obligatoires pour les classes filles
    // ==========================================================

    /**
     * Configure la table.
     *
     * La classe fille doit définir :
     *
     *  Le nom de la table ;
     *  la clé primaire ;
     *  les colonnes ;
     *  les contraintes d'unicité.
     */
    abstract protected function configure(): void;
}