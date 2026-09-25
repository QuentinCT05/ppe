use ppe;

drop trigger if exists avantajoutfonction;
drop trigger if exists avantmodificationfonction;

delimiter $$

create trigger avantajoutfonction
    before insert on fonction
    for each row
begin

    set new.repertoire = lower(trim(new.repertoire));
    set new.nom = trim(new.nom);

end$$


create trigger avantmodificationfonction
    before update on fonction
    for each row
begin

    /*
     * Le repertoire ne peut être modifié que lorsque
     * le traitement appelant positionne @trigger à 1.
     */
    if (new.repertoire <> old.repertoire)
        and (@trigger is null or @trigger <> 1) then

        signal sqlstate '45000'
            set message_text = '~Le repertoire ne peut être modifié';

    end if;


    /*
     * Le nom ne peut être modifié que lorsque
     * le traitement appelant positionne @trigger à 1.
     */
    if (new.nom <> old.nom)
        and (@trigger is null or @trigger <> 1) then

        signal sqlstate '45000'
            set message_text = '~Le nom ne peut être modifié';

    end if;


    /*
     * Normalisation des valeurs.
     * Les contraintes check de la table assurent ensuite
     * la validité du format.
     */
    set new.repertoire = lower(trim(new.repertoire));
    set new.nom = upper(trim(new.nom));

end$$

delimiter ;