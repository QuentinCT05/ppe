use ppe;

-- réinitialisation des tables
-- suppression des données dans l'ordre des dépendances (contrainte de clé étrangère) évite le recours à set foreign_key_checks = 0;
delete from droit;

-- attribution de tous les droits à l'administrateur 1
insert into droit
    select 1, repertoire
    from fonction;

select * from droit;

