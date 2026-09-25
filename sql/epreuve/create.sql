use ppe;

set default_storage_engine = innodb;

drop table if exists epreuve;

create table epreuve
(
    saison      enum('Hiver', 'Printemps', 'Été', 'Automne') not null,
    description text not null,
    date        date not null,

    constraint pk_epreuve
        primary key (saison),

    constraint uk_epreuve_date
        unique (date),

    constraint ck_epreuve_description
        check (char_length(trim(description)) >= 10)
) engine = innodb;


-- attribution des droits d'accès à l'utilisateur 'ppe'
grant select, update
    on ppe.epreuve
    to 'ppe'@'localhost';