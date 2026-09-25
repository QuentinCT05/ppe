use ppe;

-- les actions définies dans les deux premiers déclencheurs ne doivent pas s'appliquer si @trigger = 1
-- cela permet de lancer le script insert.sql sans qu'il soit bloqué par les déclencheurs
-- il suffit de tester la variable @trigger dans les déclencheurs pour savoir si on doit appliquer les règles ou pas
-- si @trigger = 1, on applique les règles
-- si @trigger = 0 ou si la variable n'existe pas, on ne les applique pas
-- pour lancer le script insert.sql, il faut donc faire : set @trigger = 1 au départ du script, puis set @trigger = 0 à la fin du script;


drop trigger if exists avantajoutepreuve;
drop trigger if exists avantmodificationepreuve;
drop trigger if exists avantsuppressionepreuve;

delimiter $$

create trigger avantajoutepreuve
    before insert on epreuve
    for each row
begin
    if (@trigger is null or @trigger <> 1) then
        signal sqlstate '45000'
            set message_text = 'L''ajout d''une épreuve n''est pas autorisé';
    end if;
end
$$


create trigger avantsuppressionepreuve
    before delete on epreuve
    for each row
begin
    if( @trigger is null or @trigger <> 1) then
    signal sqlstate '45000'
        set message_text = 'La suppression d''une épreuve n''est pas autorisée';
    end if;
end
$$


create trigger avantmodificationepreuve
    before update on epreuve
    for each row
begin

    if new.saison <> old.saison then
        signal sqlstate '45000'
            set message_text = 'La saison ne peut pas être modifiée';
    end if;

    set new.description = trim(new.description);

    if new.date <> old.date and new.date < curdate() then
        signal sqlstate '45000'
            set message_text = 'La date de l''épreuve doit être supérieure ou égale à la date du jour';
    end if;

end$$

delimiter ;