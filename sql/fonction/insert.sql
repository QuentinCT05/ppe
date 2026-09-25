use ppe;

set foreign_key_checks = 0;

delete from fonction;

-- insertion des fonctions
insert into fonction (repertoire, nom)
values
    ('epreuve', '📅 4 saisons'),
    ('membre', '👥 Membres'),
    ('photoinformation', '🖼️ Photos');

set foreign_key_checks = 1;

