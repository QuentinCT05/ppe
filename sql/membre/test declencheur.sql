use ppe;
select @@sql_mode;

# test des valeurs null
insert into membre (nom, prenom, email)
values (null, 'GUY', 'guy.verghote@saint-remi.net');
# [23000][1048] Le champ 'nom' ne peut être vide (null)

insert into membre (nom, prenom, email)
values ('VERGHOTE', null, 'guy.verghote@saint-remi.net');
# [23000][1048] Le champ 'prenom' ne peut être vide (null)

insert into membre (nom, prenom, email)
values ('VERGHOTE', 'GUY', null);
# [23000][1048] Le champ 'email' ne peut être vide (null)

-- test des valeurs vides
insert into membre (nom, prenom, email)
values ('', 'GUY', 'guy.verghote@saint-remi.net');
# [45000][1644] #Le nom  ne respecte pas le format attendu

insert into membre (nom, prenom, email)
values ('VERGHOTE', '', 'guy.verghote@saint-remi.net');
# [45000][1644] #Le prénom  ne respecte pas le format attendu

insert into membre (nom, prenom, email)
values ('VERGHOTE', 'GUY', '');
# [45000][1644] #L'email ne respecte pas le format attendu :

-- test des valeurs erronées
insert into membre (nom, prenom, email)
values ('D''halluin', 'GUY', 'guy.verghote@saint-remi.net');
# [45000][1644] #Le nom D'HALLUIN ne respecte pas le format attendu

insert into membre (nom, prenom, email)
values ('VERGHOTE', 'Derch''en', 'guy.verghote@saint-remi.net');
# [45000][1644] #Le prénom DERCH'EN ne respecte pas le format attendu

insert into membre (nom, prenom, email)
values ('VERGHOTE', 'GUY', 'test@test.');
# [45000][1644] #L'email ne respecte pas le format attendu : test@test.



-- test unicité
insert into membre (nom, prenom, email)
values ('VERGHOTE', 'GUY', 'guy.verghote@saint-remi.net');
# [45000][1644] #Ce membre est déjà inscrit


-- test des modifications non autorisées
update membre set id = 1;
# [45000][1644] #L'identifiant ne peut être modifié

update membre set nom = null where id = 1;
# [23000][1048] Le champ 'nom' ne peut être vide (null)

update membre set prenom = 'Laurent' where id = 1;
# [45000][1644] #Le prénom ne peut être modifié

update membre set login = 'admin2' where id = 1;
# [45000][1644] #Le login ne peut être modifié

update membre set login = 'admin' where id = 1;
# Accepté puisqu'aucune modification n'est effectuée

select * from membre;