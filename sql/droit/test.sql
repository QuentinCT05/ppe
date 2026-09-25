use ppe;


-- récupération d'un administrateur existant
set @id_administrateur = (select id from administrateur limit 1);

-- récupération d'une fonction existante
set @repertoire = (select repertoire from fonction limit 1);


-- 1. insertion d'un droit valide
insert into droit (idadministrateur, repertoire) values (@id_administrateur, @repertoire);
# Résultat attendu : insertion réussie


-- 2. doublon du droit
insert into droit (idadministrateur, repertoire) values (@id_administrateur, @repertoire);
# Résultat attendu : [23000][1062] violation de la contrainte 'pk_droit'


-- 3. administrateur inexistant
insert into droit (idadministrateur, repertoire) values (-1, @repertoire);
# Résultat attendu : [23000][1452] violation de la contrainte 'fk_droit_administrateur'


-- 4. fonction inexistante
insert into droit (idadministrateur, repertoire) values (@id_administrateur, 'fonction_inexistante');
# Résultat attendu : [23000][1452] violation de la contrainte 'fk_droit_fonction'


-- 5. administrateur obligatoire
insert into droit (idadministrateur, repertoire) values (null, @repertoire);
# Résultat attendu : [23000][1048] Column 'idadministrateur' cannot be null


-- 6. repertoire obligatoire
insert into droit (idadministrateur, repertoire) values (@id_administrateur, null);
# Résultat attendu : [23000][1048] Column 'repertoire' cannot be null