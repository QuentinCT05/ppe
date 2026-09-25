use ppe;


-- 1. vérification de la présence de l'épreuve d'hiver
select * from epreuve where saison = 'Hiver';
# Résultat attendu : l'épreuve d'hiver existe


-- 2. ajout interdit
insert into epreuve (saison, description, date) values ('Hiver', 'Une description suffisamment longue', '2030-01-01');
# Résultat attendu : [45000][1644] #L'ajout d'une épreuve n'est pas autorisé


-- 3. suppression interdite
delete from epreuve where saison = 'Hiver';
# Résultat attendu : [45000][1644] #La suppression d'une épreuve n'est pas autorisée


-- 4. modification de la saison interdite
update epreuve set saison = 'Printemps' where saison = 'Hiver';
# Résultat attendu : [45000][1644] #La saison ne peut pas être modifiée


-- 5. description trop courte
update epreuve set description = 'Trop court' where saison = 'Hiver';
# Résultat attendu : modification réussie car "Trop court" comporte 10 caractères


-- 6. description réellement trop courte
update epreuve set description = 'Court' where saison = 'Hiver';
# Résultat attendu : [HY000][3819] Check constraint 'ck_epreuve_description' is violated


-- 7. description composée uniquement d'espaces
update epreuve set description = '          ' where saison = 'Hiver';
# Résultat attendu : [HY000][3819] Check constraint 'ck_epreuve_description' is violated


-- 8. description avec des espaces en début et en fin
update epreuve set description = '   Une description valide   ' where saison = 'Hiver';
# Résultat attendu : modification réussie, les espaces en début et en fin sont supprimés


-- 9. date dans le passé
update epreuve set date = '2000-01-01' where saison = 'Hiver';
# Résultat attendu : [45000][1644] #La date de l'épreuve doit être supérieure ou égale à la date du jour


-- 10. date du jour
update epreuve set date = curdate() where saison = 'Hiver';
# Résultat attendu : modification réussie si aucune autre épreuve n'utilise cette date


-- 11. date future
update epreuve set date = '2099-01-01' where saison = 'Hiver';
# Résultat attendu : modification réussie


-- 12. date déjà utilisée par une autre épreuve
update epreuve set date = (select date from epreuve where saison = 'Printemps') where saison = 'Hiver';
# Résultat attendu : [23000][1062] Duplicate entry pour la contrainte unique 'uk_epreuve_date'


-- 13. saison inchangée, description modifiable
update epreuve set description = 'Nouvelle description suffisamment longue' where saison = 'Hiver';
# Résultat attendu : modification réussie


-- 14. saison inchangée, date future modifiable
update epreuve set date = '2098-12-31' where saison = 'Hiver';
# Résultat attendu : modification réussie


-- valeur erronée

update epreuve set date = '2025-02-29';
# [22001][1292] Data truncation: Incorrect date value: '2025-02-29' for column 'date' at row 1

select * from epreuve;
