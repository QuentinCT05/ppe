use ppe;


-- 1. récupération d'un membre existant
set @id_membre = (select id from membre limit 1);


-- 2. création d'un administrateur valide
insert into administrateur (id) values (@id_membre);
# Résultat attendu : insertion réussie


-- 3. doublon de l'identifiant
insert into administrateur (id) values (@id_membre);
# Résultat attendu : [23000][1062] Duplicata du champ '24' pour la clef 'administrateur.PRIMARY'


-- 4. membre inexistant
insert into administrateur (id) values (-1);
# Résultat attendu : [23000][1452] Cannot add or update a child row: a foreign key constraint fails (`ppe`.`administrateur`, CONSTRAINT `fk_administrateur_membre` FOREIGN KEY (`id`) REFERENCES `membre` (`id`) ON DELETE CASCADE ON UPDATE CASCADE)


-- 5. identifiant obligatoire
insert into administrateur (id) values (null);
# Résultat attendu : [23000][1048] Le champ 'id' ne peut être vide (null)