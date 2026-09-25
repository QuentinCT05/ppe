use ppe;

set foreign_key_checks = 0;
delete from fonction;
set foreign_key_checks = 1;


/*
 * 1. insertion correcte
 */
insert into fonction (repertoire, nom) values ('administration', 'ADMINISTRATION');
# Résultat attendu : insertion réussie


/*
 * 2. repertoire trop court
 */
insert into fonction (repertoire, nom) values ('abc', 'ADMINISTRATION');
# Résultat attendu : [HY000][3819] Check constraint 'ck_fonction_repertoire' is violated


/*
 * 3. repertoire trop long
 */
insert into fonction (repertoire, nom) values ('abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz', 'ADMINISTRATION');
# Résultat attendu : [HY000][3819] Check constraint 'ck_fonction_repertoire' is violated


/*
 * 4. repertoire contenant un espace
 */
insert into fonction (repertoire, nom) values ('admin is', 'ADMINISTRATION');
# Résultat attendu : [HY000][3819] Check constraint 'ck_fonction_repertoire' is violated


/*
 * 5. repertoire contenant un tiret
 */
insert into fonction (repertoire, nom) values ('admin-1', 'ADMINISTRATION');
# Résultat attendu : [HY000][3819] Check constraint 'ck_fonction_repertoire' is violated


/*
 * 6. repertoire contenant un underscore
 */
insert into fonction (repertoire, nom) values ('admin_1', 'ADMINISTRATION');
# Résultat attendu : [HY000][3819] Check constraint 'ck_fonction_repertoire' is violated


/*
 * 7. repertoire contenant une majuscule
 * Le trigger transforme la valeur en minuscules.
 */
insert into fonction (repertoire, nom) values ('SPORT', 'SPORT');
# Résultat attendu : insertion réussie


/*
 * 8. nom contenant des minuscules
 * Le trigger transforme la valeur en majuscules.
 */
insert into fonction (repertoire, nom) values ('tresorerie', 'tresorerie');
# Résultat attendu : insertion réussie


/*
 * 9. nom comportant plusieurs mots correctement séparés
 */
insert into fonction (repertoire, nom) values ('communication', 'SERVICE COMMUNICATION');
# Résultat attendu : insertion réussie


/*
 * 10. nom avec plusieurs espaces au milieu
 */
insert into fonction (repertoire, nom) values ('evenement', 'SERVICE  EVENEMENT');
# Résultat attendu : [HY000][3819] Check constraint 'ck_fonction_nom' is violated


/*
 * 11. nom avec un espace au début
 * Le trigger supprime l'espace avec trim().
 */
insert into fonction (repertoire, nom) values ('secretariat', ' SECRETARIAT');
# Résultat attendu : insertion réussie


/*
 * 12. nom avec un espace à la fin
 * Le trigger supprime l'espace avec trim().
 */
insert into fonction (repertoire, nom) values ('comptabilite', 'COMPTABILITE ');
# Résultat attendu : insertion réussie


/*
 * 13. nom contenant un tiret
 */
insert into fonction (repertoire, nom) values ('informatique', 'SERVICE-INFORMATIQUE');
# Résultat attendu : [HY000][3819] Check constraint 'ck_fonction_nom' is violated


/*
 * 14. nom contenant un underscore
 */
insert into fonction (repertoire, nom) values ('logistique', 'SERVICE_LOGISTIQUE');
# Résultat attendu : [HY000][3819] Check constraint 'ck_fonction_nom' is violated


/*
 * 15. nom contenant un chiffre
 */
insert into fonction (repertoire, nom) values ('materiel', 'MATERIEL2');
# Résultat attendu : [HY000][3819] Check constraint 'ck_fonction_nom' is violated


/*
 * 16. nom vide
 */
insert into fonction (repertoire, nom) values ('bureau', '');
# Résultat attendu : [HY000][3819] Check constraint 'ck_fonction_nom' is violated


/*
 * 17. repertoire vide
 */
insert into fonction (repertoire, nom) values ('', 'BUREAU');
# Résultat attendu : [HY000][3819] Check constraint 'ck_fonction_repertoire' is violated


/*
 * 18. repertoire null
 */
insert into fonction (repertoire, nom) values (null, 'BUREAU');
# Résultat attendu : [23000][1048] Column 'repertoire' cannot be null


/*
 * 19. nom null
 */
insert into fonction (repertoire, nom) values ('nouveau', null);
# Résultat attendu : [23000][1048] Column 'nom' cannot be null


/*
 * 20. doublon de clé primaire
 */
insert into fonction (repertoire, nom) values ('administration', 'AUTRE NOM');
# Résultat attendu : [23000][1062] Duplicate entry 'administration' for key 'fonction.PRIMARY'


/*
 * 21. modification du nom sans jeton
 */
update fonction set nom = 'NOUVEAU NOM' where repertoire = 'administration';
# Résultat attendu : [45000][1644] #Le nom ne peut être modifié


/*
 * 22. modification du repertoire sans jeton
 */
update fonction set repertoire = 'nouveau' where repertoire = 'administration';
# Résultat attendu : [45000][1644] #Le repertoire ne peut être modifié


/*
 * 23. modification du nom avec le jeton
 */
set @trigger = 1;
update fonction set nom = 'NOUVEAU NOM' where repertoire = 'administration';
set @trigger = null;
# Résultat attendu : modification réussie


/*
 * 24. modification du repertoire avec le jeton
 */
set @trigger = 1;
update fonction set repertoire = 'admin' where repertoire = 'administration';
set @trigger = null;
# Résultat attendu : modification réussie


/*
 * 25. modification du nom avec plusieurs espaces
 * Le trigger autorise la modification mais le check la refuse.
 */
set @trigger = 1;
update fonction set nom = 'NOUVEAU  NOM' where repertoire = 'admin';
set @trigger = null;
# Résultat attendu : [HY000][3819] Check constraint 'ck_fonction_nom' is violated


/*
 * 26. modification du repertoire avec un caractère interdit
 * Le trigger autorise la modification mais le check la refuse.
 */
set @trigger = 1;
update fonction set repertoire = 'ad-' where repertoire = 'admin';
set @trigger = null;
# Résultat attendu : [HY000][3819] Check constraint 'ck_fonction_repertoire' is violated


/*
 * 27. modification du repertoire avec des majuscules
 * Le trigger transforme la valeur en minuscules.
 */
set @trigger = 1;
update fonction set repertoire = 'BUREAU2' where repertoire = 'bureau';
set @trigger = null;
# Résultat attendu : modification réussie


/*
 * 28. modification du nom avec des minuscules
 * Le trigger transforme la valeur en majuscules.
 */
set @trigger = 1;
update fonction set nom = 'nouveau nom' where repertoire = 'BUREAU2';
set @trigger = null;
# Résultat attendu : modification réussie


/*
 * nettoyage final
 */
set foreign_key_checks = 0;
delete from fonction;
set foreign_key_checks = 1;