use ppe;

set default_storage_engine = InnoDb;
set foreign_key_checks = 0;

drop table if exists droit;

create table droit
(
    idAdministrateur int         not null,
    repertoire       varchar(50) not null,
    constraint pk_droit primary key (idAdministrateur, repertoire),
    constraint fk_droit_administrateur
        foreign key (idAdministrateur)
            references administrateur (id) on delete cascade,
    constraint fk_droit_fonction
        foreign key (repertoire)
            references fonction (repertoire) on delete cascade on update cascade
)
    engine = innodb
    default character set = utf8mb4
    collate = utf8mb4_unicode_ci;

set foreign_key_checks = 1;

-- attribution des droits d'accès à l'utilisateur 'ppe'
grant select, insert, delete
    on ppe.droit
    to 'ppe'@'localhost';