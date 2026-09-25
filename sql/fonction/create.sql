use ppe;

set default_storage_engine = innodb;
set foreign_key_checks = 0;

drop table if exists fonction;

create table fonction
(
    repertoire varchar(50)  not null,
    nom        varchar(150) not null,

    constraint pk_fonction
        primary key (repertoire),

    constraint ck_fonction_repertoire
        check (
            regexp_like(repertoire, '^[a-z0-9]{4,50}$', 'c')
                and not regexp_like(
                    repertoire,
                    '^(con|prn|aux|nul|com[1-9]|lpt[1-9])$',
                    'c'
                        )
            )

)
    engine = innodb
    default character set = utf8mb4
    collate = utf8mb4_unicode_ci;

set foreign_key_checks = 1;

-- attribution des droits d'accès à l'utilisateur 'ppe'
grant select, insert
    on ppe.fonction
    to 'ppe'@'localhost';