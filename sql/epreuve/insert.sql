use ppe;

-- passage  en mode 'admin' afin de pouvoir insérer des données dans la table epreuve
set @trigger = 1;

delete from epreuve;

insert into epreuve (saison, description, date)
values ('hiver',
        '<p><strong>Label r&eacute;gional FFA qualificatif au championnat de France sur l\'ensemble des courses.</strong></p>\n<p>Les Tarifs</p>\n<ul>\n<li>Licenci&eacute;s FFA&nbsp; &nbsp; &nbsp; &nbsp;: 8&nbsp;euros&nbsp;</li>\n<li>Non licenci&eacute;s FFA : 10&nbsp;euros&nbsp;</li>\n<li>Gratuit&eacute; pour les membres de l\'Amicale du Val de Somme</li>\n</ul>',
        '2027-02-14'),
       ('Printemps',
        '<p>Les tarifs</p>\n<ul>\n<li>Licenci&eacute;s et non licenci&eacute;s &nbsp;: <strong>8 euros&nbsp;</strong></li>\n<li>Gratuit&eacute; pour les membres de l\'Amicale du val de Somme</li>\n</ul>',
        '2027-05-09'),
       ('Été',
        '<p>Les tarifs</p>\n<ul>\n<li>Licenci&eacute;s et non licenci&eacute;s &nbsp;: <strong>8 euros&nbsp;</strong></li>\n<li>Gratuit&eacute; pour les membres de l\'Amicale du val de Somme</li>\n</ul>\n<p>&nbsp;</p>',
        '2027-07-04'),
       ('Automne',
        '<p><strong>Label r&eacute;gional FFA qualificatif au championnat de France sur l\'ensemble des courses</strong></p>\n<p>Les Tarifs</p>\n<ul>\n<li>Licenci&eacute;s FFA&nbsp; &nbsp; &nbsp; &nbsp;: 8&nbsp;euros&nbsp;</li>\n<li>Non licenci&eacute;s FFA : 10&nbsp;euros&nbsp;</li>\n<li>Gratuit&eacute; pour les membres de l\'Amicale du val de Somme</li>\n</ul>\n<p>Pour cette finale, plus de 2000 &euro; de prime seront distribu&eacute;s et un lot de grande qualit&eacute; sera offert &agrave; chaque arrivant.</p>\n<p>Il reste aussi des kits \"4 &eacute;ditions + 4 m&eacute;dailles\" jusqu\'&agrave; &eacute;puisement des stocks.</p>',
        '2026-11-08');

set @trigger = null;



