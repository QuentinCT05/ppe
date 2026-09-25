SET default_storage_engine=InnoDb;
--

set foreign_key_checks = 0;
Drop database if exists ppe;
set foreign_key_checks = 1;

create database ppe
    character set utf8mb4
    collate utf8mb4_unicode_ci;

-- ordre de lancement des scripts : create, declencheur, test et insert

-- 1. user.sql
-- 2. répertoire epreuve
-- 3. répertoire  fonction
-- 4. répertoire membre
-- 5. répertoire admministrateur
-- 6. répertoire droit
-- 7. répertoire resultat (un seul scriptresultat.slq pour les 3 tables)


