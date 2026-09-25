use ppe;

set default_storage_engine = innodb;
set foreign_key_checks = 0;

drop table if exists administrateur;

create table administrateur
(
    id int not null,

    constraint pk_administrateur
        primary key (id),

    constraint fk_administrateur_membre
        foreign key (id)
            references membre (id)
            on delete cascade
            on update cascade
) engine = innodb;

set foreign_key_checks = 1;

-- attribution des droits d'accès à l'utilisateur 'ppe'
grant select, insert, delete
    on administrateur
    to 'ppe'@'localhost';