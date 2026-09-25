use ppe;

drop trigger if exists avantAjoutMembre;
drop trigger if exists avantModificationMembre;

delimiter $$

create trigger avantAjoutMembre
    before insert
    on membre
    for each row
begin
    declare nb int;
    declare nouveauLogin varchar(50)
        character set utf8mb4
        collate utf8mb4_unicode_ci;

    declare baseLogin varchar(50)
        character set utf8mb4
        collate utf8mb4_unicode_ci;

    --  normalisation du nom et du prénom
    set new.nom = upper(trim(new.nom));
    set new.prenom = upper(trim(new.prenom));

    -- création automatique du login première lettre du prénom suivie du nom sans espace
    set baseLogin = concat(lower(left(new.prenom, 1)), lower(replace(new.nom, ' ', '')));

    set nouveauLogin = baseLogin;
    set nb = 1;

    while exists (select 1 from membre where login = nouveauLogin)
        do
            set nouveauLogin = concat(baseLogin, nb);
            set nb = nb + 1;
        end while;

    set new.login = nouveauLogin;

    -- initialisation du mot de passe par défaut
    set new.password = sha2('0000', 256);
end


$$


create trigger avantModificationMembre
    before update
    on membre
    for each row
begin

    /*
     * l'identifiant ne peut jamais être modifié
     */
    if new.id != old.id then
        signal sqlstate '45000' set message_text = 'L''identifiant ne peut être modifié';
    end if;

    /*
     * le nom et le prénom ne sont modifiables que par root
     */
    if (new.nom != old.nom) then
        if substring_index(user(), '@', 1) <> 'root' then
            signal sqlstate '45000' set message_text = 'Le nom ne peut être modifié';
        else
            set new.nom = upper(trim(new.nom));
        end if;
    end if;

    if (new.prenom != old.prenom) then
        if substring_index(user(), '@', 1) <> 'root' then
            signal sqlstate '45000' set message_text = 'Le prénom ne peut être modifié';
        else
            set new.prenom = upper(trim(new.prenom));
        end if;
    end if;


    /*
     * le login ne peut pas être modifié
     * sauf avec le jeton interne @trigger = 1
     */
    if (new.login is null or new.login != old.login) and substring_index(user(), '@', 1) <> 'root' then
        signal sqlstate '45000' set message_text = 'Le login ne peut être modifié';
    end if;

end
$$

delimiter ;