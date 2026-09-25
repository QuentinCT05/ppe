use ppe;

set default_storage_engine = InnoDb;
set foreign_key_checks = 0;

drop table if exists membre;

create table membre
(
    id              int auto_increment,
    login           varchar(50)  not null,
    password        varchar(64)  not null,
    nom             varchar(30)  not null,
    prenom          varchar(50)  not null,
    email           varchar(100) not null,
    telephone       varchar(10)  null,
    photo           varchar(100) null,
    autMail         tinyint      not null default 0,
    saison          smallint     not null,

    constraint pk_membre
        primary key (id),

    constraint uk_membre_login
        unique (login),

    constraint uk_membre_nom_prenom_email
        unique (nom, prenom, email),

    constraint ck_membre_nom
        check (nom regexp '^[A-ZÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝŸÆŒ]+( [A-ZÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝŸÆŒ]+)*$'),

    constraint ck_membre_prenom
        check (prenom regexp '^[A-ZÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝŸÆŒ]+( [A-ZÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝŸÆŒ]+)*$'),

    constraint ck_membre_email
        check (email regexp '^[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*[\.][a-zA-Z]{2,4}$'),

    constraint ck_membre_telephone
        check (            telephone is null                or telephone regexp '^0[1-79][0-9]{8}$'            ),

    constraint ck_membre_autmail
        check (autMail in (0, 1))
)
    engine = innodb
    default character set = utf8mb4
    collate = utf8mb4_unicode_ci;


set foreign_key_checks = 1;

-- attribution des droits d'accès à l'utilisateur 'ppe'
grant select, insert, update(telephone, photo, autMail), delete
    on ppe.membre
    to 'ppe'@'localhost';