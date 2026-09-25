use ppe;

set foreign_key_checks = 0;

delete from membre;


-- ============================================================
-- Initialisation des saisons
-- ============================================================

set @annee = year(curdate());
set @saison_courante = @annee;
set @saison_suivante = @annee + 1;


-- ============================================================
-- Insertion des membres
-- ============================================================

-- Réinitialisation du compteur
alter table membre auto_increment = 1;


-- ============================================================
-- Membres de la saison courante
-- ============================================================

insert into membre (nom, prenom, email, photo, saison)
values
    ('VERGHOTE', 'GUY', 'guy.verghote@saint-remi.net', 'ac.png', @saison_courante),
    ('BAKASSI', 'SOULAIMANE', 'soulaimane.bakassi@saint-remi.net', 'bakassi soulaimane.jpg', @saison_courante),
    ('BERNARD', 'JULIEN', 'julien.bernard@saint-remi.net', 'bernard julien.jpg', @saison_courante),
    ('BOULARBI', 'MEDDHY', 'meddhy.boularbi@saint-remi.net', 'boularbi meddhy.jpg', @saison_courante),
    ('CARON', 'ADAM', 'adam.caron@saint-remi.net', 'caron adam.jpg', @saison_courante),
    ('CHARKAOUI', 'RAYANE', 'rayane.charkaoui@saint-remi.net', 'charkaoui rayane.jpg', @saison_courante),
    ('CHASTAGNER', 'ARTHUR', 'arthur.chastagner@saint-remi.net', 'chastagner arthur.jpg', @saison_courante),
    ('COULON', 'ALEXANDRE', 'alexandre.coulon@saint-remi.net', 'coulon alexandre.jpg', @saison_courante),
    ('DUBOIS', 'ALEXANDRE', 'alexandre.dubois@saint-remi.net', 'dubois alexandre.jpg', @saison_courante),
    ('JOSSE', 'THOMAS', 'thomas.josse@saint-remi.net', 'josse thomas.jpg', @saison_courante),
    ('LE CANU', 'MATHIS', 'mathis.le-canu@saint-remi.net', 'le canu mathis.jpg', @saison_courante),
    ('LION', 'ZIGGY', 'ziggy.lion@saint-remi.net', 'lion ziggy.jpg', @saison_courante),
    ('LONGBY', 'RENEDI', 'renedi.longby@saint-remi.net', 'longby renedi.jpg', @saison_courante),
    ('LOURDEL', 'MATHIS', 'mathis.lourdel@saint-remi.net', 'lourdel mathis.jpg', @saison_courante),
    ('MORALES', 'SIMON', 'simon.morales@saint-remi.net', 'morales simon.jpg', @saison_courante),
    ('NEDELEC', 'FLORE', 'flore.nedelec@saint-remi.net', 'nedelec flore.jpg', @saison_courante),
    ('PARIS', 'THOMAS', 'thomas.paris@saint-remi.net', 'paris thomas.jpg', @saison_courante),
    ('RICHARD', 'TONNY', 'tonny.richard@saint-remi.net', null, @saison_courante),
    ('RICHARD', 'TOM', 'tom.richard@saint-remi.net', 'richard tom.jpg', @saison_courante),
    ('ROELENS', 'GABRIEL', 'gabriel.roelens@saint-remi.net', 'roelens gabriel.jpg', @saison_courante),
    ('SOUKTANI', 'LEO', 'leo.souktani@saint-remi.net', 'souktani leo.jpg', @saison_courante),
    ('SUBERU', 'MOUBARAK', 'moubarak.suberu@saint-remi.net', 'suberu moubarak.jpg', @saison_courante),
    ('TISON', 'CLAIRE', 'claire.tison@saint-remi.net', 'tison claire.jpg', @saison_courante),
    ('BALDE', 'AISSATOU', 'aissatou.balde@saint-remi.net', 'balde aissatou.jpg', @saison_courante),
    ('BOILET', 'KAMERON', 'kameron.boilet@saint-remi.net', 'boilet kameron.jpg', @saison_courante),
    ('BOULLY', 'ALEXANDRE', 'alexandre.boully@saint-remi.net', 'boully alexandre.jpg', @saison_courante),
    ('CAZIN', 'TOM', 'tom.cazin@saint-remi.net', 'cazin tom.jpg', @saison_courante),
    ('DIANI', 'ISMAEL', 'ismael.diani@saint-remi.net', 'diani ismael.jpg', @saison_courante),
    ('DUMONT', 'HUGO', 'hugo.dumont@saint-remi.net', 'dumont hugo.jpg', @saison_courante),
    ('DUPRESSOIR', 'MATHIEU', 'mathieu.dupressoir@saint-remi.net', 'dupressoir mathieu.jpg', @saison_courante),
    ('FOULON', 'MATHIS', 'mathys.foulon@saint-remi.net', 'foulon mathis.jpg', @saison_courante),
    ('GARNIER', 'KYLLIAN', 'kyllian.garnier@saint-remi.net', 'garnier kyllian.jpg', @saison_courante),
    ('KARACA', 'ATTILA', 'attila.karaca@saint-remi.net', 'karaca attila.jpg', @saison_courante),
    ('MARGOTIN', 'PAUL', 'paul.margotin@saint-remi.net', 'margotin paul.jpg', @saison_courante),
    ('MERCIER', 'ALEXI', 'alexi.mercier@saint-remi.net', 'mercier alexi.jpg', @saison_courante),
    ('MERVILLE', 'LUCAS', 'lucas.merville@saint-remi.net', 'merville lucas.jpg', @saison_courante),
    ('MORTELETTE', 'CLEMENT', 'clement.mortelette@saint-remi.net', 'mortelette clement.jpg', @saison_courante),
    ('NOUHI', 'MARWAN', 'marwan.nouhi@saint-remi.net', 'nouhi marwan.jpg', @saison_courante),
    ('ROUSELLE', 'ETIENNE', 'etienne.rouselle@saint-remi.net', 'rouselle etienne.jpg', @saison_courante),
    ('VASSEUR', 'LORENZO', 'lorenzo.vasseur@saint-remi.net', 'vasseur lorenzo.jpg', @saison_courante),
    ('YILDIZ', 'MUHAMMEDALI', 'muhammedali.yildiz@saint-remi.net', 'yildiz muhammedali.jpg', @saison_courante),
    ('ZON', 'JEREMY', 'jeremy.zon@saint-remi.net', 'zon jeremy.jpg', @saison_courante);


-- ============================================================
-- Membres de la saison suivante
-- ============================================================

insert into membre (nom, prenom, email, photo, saison)
values
    ('BEN AHMED', 'ILYES', 'yles.benahmed@saint-remi.net', 'ben ahmed ilyes.jpg', @saison_suivante),
    ('BENAALMA', 'YANIS', 'yanis.benaalma@saint-remi.net', 'benaalma yanis.jpg', @saison_suivante),
    ('BOSSIAUX', 'ALEXANDRE', 'alexandre.bossiaux@saint-remi.net', 'bossiaux alexandre.jpg', @saison_suivante),
    ('CAUET', 'QUENTIN', 'quentin.cauet@saint-remi.net', 'cauet quentin.jpg', @saison_suivante),
    ('CZABAN', 'MATHIAS', 'mathias.czaban@saint-remi.net', 'czaban mathias.jpg', @saison_suivante),
    ('DAUTREVAUX', 'THEO', 'theo.dautrevaux@saint-remi.net', 'dautrevaux theo.jpg', @saison_suivante),
    ('DAY RODY', 'LUCAS', 'lucas.dayrody@saint-remi.net', 'day rody lucas.jpg', @saison_suivante),
    ('DEGREMONT', 'MAXENCE', 'maxence.degremont@saint-remi.net', 'degremont maxence.jpg', @saison_suivante),
    ('DELIGNIES', 'KYLIAN', 'kylian.delignies@saint-remi.net', 'delignies kylian.jpg', @saison_suivante),
    ('DUBOS', 'MATHIS', 'mathis.dubos@saint-remi.net', 'dubos mathis.jpg', @saison_suivante),
    ('DUSSY', 'LILIAN', 'lilian.dussy@saint-remi.net', 'dussy lilian.jpg', @saison_suivante),
    ('FORBRAS', 'NOE', 'noe.forbras@saint-remi.net', 'forbras noe.jpg', @saison_suivante),
    ('GUILLEUX', 'MAEL', 'mael.guilleux@saint-remi.net', 'guilleux mael.jpg', @saison_suivante),
    ('HOUDERRANI', 'KARIM', 'karim.houderrani@saint-remi.net', 'houderrani karim.jpg', @saison_suivante),
    ('KHUDOYAN', 'DIANA', 'diana.khudoyan@saint-remi.net', 'khudoyan diana.jpg', @saison_suivante),
    ('KOUAM KAMDEM', 'NOE', 'noe.kouamkamdem@saint-remi.net', 'kouam kamdem noe.jpg', @saison_suivante),
    ('KWIZERA', 'EDMOND', 'edmond.kwizera@saint-remi.net', 'kwizera edmond.jpg', @saison_suivante),
    ('LAVARENNE', 'JOLAN', 'jolan.lavarenne@saint-remi.net', 'lavarenne jolan.jpg', @saison_suivante),
    ('LECLERCQ', 'ZACKARY', 'zackary.leclercq@saint-remi.net', 'leclercq zackary.jpg', @saison_suivante),
    ('LESIEUR', 'TOM', 'tom.lesieur@saint-remi.net', 'lesieur tom.jpg', @saison_suivante),
    ('LOMBARD', 'THOMAS', 'thomas.lombard@saint-remi.net', 'lombard thomas.jpg', @saison_suivante),
    ('MARQUANT', 'ALEXANDRE', 'alexandre.marquant@saint-remi.net', 'marquant alexandre.jpg', @saison_suivante),
    ('METGY', 'LEO', 'leo.metgy@saint-remi.net', 'metgy leo.jpg', @saison_suivante),
    ('MUKADI KABOMBO', 'JOSUE', 'josue.mukadikabombo@saint-remi.net', 'mukadi kabombo josue.jpg', @saison_suivante),
    ('PODLUNSEK', 'CORENTIN', 'corentin.podlunsek@saint-remi.net', 'podlunsek corentin.jpg', @saison_suivante),
    ('SCHAAF', 'NAEL', 'nael.schaaf@saint-remi.net', 'schaaf nael.jpg', @saison_suivante),
    ('SECLET', 'BRYAN', 'bryan.seclet@saint-remi.net', 'seclet bryan.jpg', @saison_suivante),
    ('SFIH', 'NOURA', 'noura.sfih@saint-remi.net', 'sfih noura.jpg', @saison_suivante),
    ('SOW', 'MOUHAMED', 'mouhamed.sow@saint-remi.net', 'sow mouhamed.jpg', @saison_suivante),
    ('TOUDJI', 'RHILES', 'rhiles.toudji@saint-remi.net', 'toudji rhiles.jpg', @saison_suivante),
    ('VARLET', 'MATTEO', 'matteo.varlet@saint-remi.net', 'varlet matteo.jpg', @saison_suivante);


-- ============================================================
-- Modification du membre 1
-- ============================================================
-- Modification du login interdite par le trigger sauf avec le jeton


update membre
set login = 'admin',
    saison = @saison_suivante
where id = 1;



-- ============================================================
-- Fin
-- ============================================================

set foreign_key_checks = 1;