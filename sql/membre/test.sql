use ppe;

-- ============================================================
-- remise à zéro de la table
-- ============================================================

set foreign_key_checks = 0;
delete from membre;
set foreign_key_checks = 1;


-- ============================================================
-- tests d'insertion
-- ============================================================

-- 1. insertion valide
insert into membre (login, password, nom, prenom, email) values ('', 'test', 'DUPONT', 'MARC', 'marc.dupont@test.fr');
# Résultat attendu : insertion réussie, login généré : mdupont


-- 2. espace en début du nom
insert into membre (login, password, nom, prenom, email) values ('', 'test', ' DUPUIS', 'PAUL', 'paul.dupuis@test.fr');
# Résultat attendu : insertion réussie, nom enregistré : DUPUIS, login généré : pdupuis


-- 3. espace en fin du nom
insert into membre (login, password, nom, prenom, email) values ('', 'test', 'MARTIN ', 'JEAN', 'jean.martin@test.fr');
# Résultat attendu : insertion réussie, nom enregistré : MARTIN, login généré : jmartin


-- 4. espace en début du prénom
insert into membre (login, password, nom, prenom, email) values ('', 'test', 'BERNARD', ' LUC', 'luc.bernard@test.fr');
# Résultat attendu : insertion réussie, prénom enregistré : LUC, login généré : lbernard


-- 5. espace en fin du prénom
insert into membre (login, password, nom, prenom, email) values ('', 'test', 'LEGRAND', 'ALAIN ', 'alain.legrand@test.fr');
# Résultat attendu : insertion réussie, prénom enregistré : ALAIN, login généré : alegrand


-- 6. plusieurs espaces dans le nom
insert into membre (login, password, nom, prenom, email) values ('', 'test', 'DE  GAULLE', 'CHARLES', 'charles.degaulle@test.fr');
# Résultat attendu : [HY000][3819] Check constraint 'ck_membre_nom' is violated.


-- 7. plusieurs espaces dans le prénom
insert into membre (login, password, nom, prenom, email) values ('', 'test', 'DURAND', 'JEAN  PIERRE', 'jean.pierre.durand@test.fr');
# Résultat attendu : [HY000][3819] Check constraint 'ck_membre_prenom' is violated.


-- 8. chiffre dans le nom
insert into membre (login, password, nom, prenom, email) values ('', 'test', 'DUPONT1', 'LUC', 'luc.dupont1@test.fr');
# Résultat attendu : [HY000][3819] Check constraint 'ck_membre_nom' is violated.


-- 9. chiffre dans le prénom
insert into membre (login, password, nom, prenom, email) values ('', 'test', 'MARTIN', 'JEAN1', 'jean1.martin@test.fr');
# Résultat attendu : [HY000][3819] Check constraint 'ck_membre_prenom' is violated.


-- 10. caractère interdit dans le nom
insert into membre (login, password, nom, prenom, email) values ('', 'test', 'DUPONT-', 'PAUL', 'paul.dupont@test.fr');
# Résultat attendu : [HY000][3819] Check constraint 'ck_membre_nom' is violated.


-- 11. caractère interdit dans le prénom
insert into membre (login, password, nom, prenom, email) values ('', 'test', 'DUPONT', 'PAUL-', 'paul2.dupont@test.fr');
# Résultat attendu : [HY000][3819] Check constraint 'ck_membre_prenom' is violated.


-- ============================================================
-- tests de l'adresse email
-- ============================================================

-- 12. email valide
insert into membre (login, password, nom, prenom, email) values ('', 'test', 'ROBERT', 'ALAIN', 'alain.robert@test.fr');
# Résultat attendu : insertion réussie


-- 13. email sans @
insert into membre (login, password, nom, prenom, email) values ('', 'test', 'ROUSSEAU', 'PIERRE', 'pierre.rousseau.test.fr');
# Résultat attendu : [HY000][3819] Check constraint 'ck_membre_email' is violated.


-- 14. email sans domaine
insert into membre (login, password, nom, prenom, email) values ('', 'test', 'ROUSSEAU', 'JEAN', 'jean.rousseau@');
# Résultat attendu : [HY000][3819] Check constraint 'ck_membre_email' is violated.


-- 15. extension de domaine trop courte
insert into membre (login, password, nom, prenom, email) values ('', 'test', 'PETIT', 'LUC', 'luc.petit@a.c');
# Résultat attendu : [HY000][3819] Check constraint 'ck_membre_email' is violated.


-- 16. extension de domaine trop longue
insert into membre (login, password, nom, prenom, email) values ('', 'test', 'PETIT', 'PAUL', 'paul.petit@exemple.abcdef');
# Résultat attendu : [HY000][3819] Check constraint 'ck_membre_email' is violated.


-- ============================================================
-- tests du téléphone
-- ============================================================

-- 17. téléphone valide
insert into membre (login, password, nom, prenom, email, telephone) values ('', 'test', 'GIRARD', 'ALAIN', 'alain.girard@test.fr', '0612345678');
# Résultat attendu : insertion réussie


-- 18. téléphone absent
insert into membre (login, password, nom, prenom, email, telephone) values ('', 'test', 'GARNIER', 'LUC', 'luc.garnier@test.fr', null);
# Résultat attendu : insertion réussie


-- 19. téléphone trop court
insert into membre (login, password, nom, prenom, email, telephone) values ('', 'test', 'FAURE', 'JEAN', 'jean.faure@test.fr', '061234567');
# Résultat attendu : [HY000][3819] Check constraint 'ck_membre_telephone' is violated.


-- 20. téléphone trop long
insert into membre (login, password, nom, prenom, email, telephone) values ('', 'test', 'FAURE', 'PAUL', 'paul.faure@test.fr', '06123456789');
# Résultat attendu : [HY000][3819] Check constraint 'ck_membre_telephone' is violated.


-- 21. téléphone ne commençant pas par 0
insert into membre (login, password, nom, prenom, email, telephone) values ('', 'test', 'MERCIER', 'ERIC', 'eric.mercier@test.fr', '712345678');
# Résultat attendu : [HY000][3819] Check constraint 'ck_membre_telephone' is violated.


-- 22. téléphone commençant par 08
insert into membre (login, password, nom, prenom, email, telephone) values ('', 'test', 'MERCIER', 'ANDRE', 'andre.mercier@test.fr', '0812345678');
# Résultat attendu : [HY000][3819] Check constraint 'ck_membre_telephone' is violated.


-- ============================================================
-- tests des contraintes d'unicité et de génération du login
-- ============================================================

-- 23. premier login pdupuis
insert into membre (login, password, nom, prenom, email) values ('', 'test', 'DUPUIS', 'PIERRE', 'pierre.dupuis@test.fr');
# Résultat attendu : insertion réussie, login généré : pdupuis


-- 24. deuxième personne avec le même nom et la même première lettre du prénom
insert into membre (login, password, nom, prenom, email) values ('', 'test', 'DUPUIS', 'PAUL', 'paul.dupuis2@test.fr');
# Résultat attendu : insertion réussie, login généré : pdupuis1


-- 25. doublon nom + prénom + email
insert into membre (login, password, nom, prenom, email) values ('', 'test', 'DUPONT', 'MARC', 'marc.dupont@test.fr');
# Résultat attendu : [23000][1062] Duplicate entry 'DUPONT-MARC-marc.dupont@test.fr' for key 'uk_membre'


-- 26. même email avec nom et prénom différents
insert into membre (login, password, nom, prenom, email) values ('', 'test', 'DUPONT', 'JEAN', 'marc.dupont@test.fr');
# Résultat attendu : insertion réussie


-- ============================================================
-- tests des contraintes not null
-- ============================================================

-- 27. nom absent
insert into membre (login, password, nom, prenom, email) values ('', 'test', null, 'JEAN', 'jean.test@test.fr');
# Résultat attendu : [23000][1048] Le champ 'login' ne peut être vide (null)


-- 28. prénom absent
insert into membre (login, password, nom, prenom, email) values ('', 'test', 'DUPONT', null, 'jean.test2@test.fr');
# Résultat attendu : [23000][1048] Le champ 'prenom' ne peut être vide (null)


-- 29. email absent
insert into membre (login, password, nom, prenom, email) values ('', 'test', 'DUPONT', 'JEAN', null);
# Résultat attendu : [23000][1048] Le champ 'email' ne peut être vide (null)


-- 30. mot de passe absent
insert into membre (login, password, nom, prenom, email) values ('', null, 'DUPONT', 'JEAN', 'jean.test3@test.fr');
# Résultat attendu : [23000][1048] Le champ 'password' ne peut être vide (null)


-- ============================================================
-- tests des modifications
-- ============================================================

-- 31. modification du nom sans autorisation
update membre set nom = 'MARTIN' where login = 'mdupont';
# Résultat attendu : [45000][1644] #Le nom ne peut être modifié


-- 32. modification du prénom sans autorisation
update membre set prenom = 'JEAN' where login = 'mdupont';
# Résultat attendu : [45000][1644] #Le prénom ne peut être modifié


-- 33. modification du login sans autorisation
update membre set login = 'nouveaulogin' where login = 'mdupont';
# Résultat attendu : [45000][1644] #Le login ne peut être modifié


-- 34. modification d'un email valide
update membre set email = 'nouveau.email@test.fr' where login = 'mdupont';
# Résultat attendu : modification réussie


-- 35. modification d'un email invalide
update membre set email = 'email-invalide' where login = 'mdupont';
# Résultat attendu : [HY000][3819] Check constraint 'ck_membre_email' is violated.


-- 36. modification d'un téléphone valide
update membre set telephone = '0612345678' where login = 'mdupont';
# Résultat attendu : modification réussie


-- 37. modification d'un téléphone invalide
update membre set telephone = '123456789' where login = 'mdupont';
# Résultat attendu : [HY000][3819] Check constraint 'ck_membre_telephone' is violated.


-- 38. modification de l'identifiant
update membre set id = id + 100 where login = 'mdupont';
# Résultat attendu : [45000][1644] #L'identifiant ne peut être modifié


-- ============================================================
-- nettoyage final
-- ============================================================

set foreign_key_checks = 0;
delete from membre;
set foreign_key_checks = 1;