DROP TABLE IF EXISTS cours;

CREATE TABLE cours(
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    nom CHAR(4) NOT NULL,
    duree INTEGER NOT NULL
);

DROP TABLE IF EXISTS departement;

CREATE TABLE departement(
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    nom CHAR(50) NOT NULL
);

DROP TABLE IF EXISTS promotion;

CREATE TABLE promotion(
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    annee INTEGER NOT NULL,
    departement_id INTEGER NOT NULL,
    FOREIGN KEY (departement_id) REFERENCES departement (id)
);

DROP TABLE IF EXISTS salle;

CREATE TABLE salle(
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    nom CHAR(10)
);

DROP TABLE IF EXISTS type_utilisateur;

CREATE TABLE type_utilisateur(
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    type CHAR(15)	
);

DROP TABLE IF EXISTS modules;

CREATE TABLE modules(
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    nom CHAR(50),
    prof_id INTEGER,
    FOREIGN KEY (prof_id) REFERENCES utilisateus (id)
);

DROP TABLE IF EXISTS utilisateurs;

CREATE TABLE utilisateurs(
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    nom CHAR(50) NOT NULL,
    prenom CHAR(50) NOT NULL,
    type_utilisateur_id INTEGER NOT NULL,
    promo_id INTEGER,
    FOREIGN KEY (type_utilisateur_id) REFERENCES type_utilisateur (id),
    FOREIGN KEY (promo_id) REFERENCES promotion (id)
);

DROP TABLE IF EXISTS creneaux;

CREATE TABLE creneaux(
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  	module_id INTEGER NOT NULL,
    date_cours date NOT NULL,
    heure_debut INTEGER NOT NULL,
    heure_fin INTEGER NOT NULL,
    cours_id INTEGER NOT NULL,
    salle_id INTEGER NOT NULL,
    promo_id INTEGER NOT NULL,
  	prof_id INTEGER NOT NULL,
    FOREIGN KEY (module_id) REFERENCES modules (id),
    FOREIGN KEY (cours_id) REFERENCES cours (id),
    FOREIGN KEY (salle_id) REFERENCES salle (id),
    FOREIGN KEY (promo_id) REFERENCES promotion (id),
    FOREIGN KEY (prof_id) REFERENCES utilisateurs (id)
);

INSERT INTO departement (nom) VALUES
    ('Maths'),
    ('Info'),
    ('SVT'),
    ('Chimie');

INSERT INTO type_utilisateur (type) VALUES
    ('etudiants'),
    ('prof'),
    ('admin');

INSERT INTO cours (nom, duree) VALUES
    ('TD', 100),
    ('TP', 100),
    ('Cours', 130);

INSERT INTO salle (nom) VALUES
    ('S21'),
    ('S22'),
    ('S23'),
    ('S24'),
    ('S25'),
    ('G100'),
    ('G101'),
    ('G102'),
    ('G103'),
    ('G104'),
    ('G105'),
    ('G200'),
    ('G201'),
    ('G202'),
    ('G203'),
    ('G204'),
    ('G205'),
    ('D100'),
    ('D101'),
    ('D102'),
    ('D103'),
    ('D104'),
    ('D105'),
    ('D200'),
    ('D201'),
    ('D202'),
    ('D203'),
    ('D204'),
    ('D205'),
    ('C100'),
    ('C101'),
    ('C102'),
    ('C103'),
    ('C104'),
    ('C105'),
    ('C200'),
    ('C201'),
    ('C202'),
    ('C203'),
    ('C204'),
    ('C205');

INSERT INTO promotion (annee,departement_id) VALUES
    (1,1),
    (2,1),
    (3,1),
    (4,1),
    (5,1),
    (1,2),
    (2,2),
    (3,2),
    (4,2),
    (5,2),
    (1,3),
    (2,3),
    (3,3),
    (4,3),
    (5,3),
    (1,4),
    (2,4),
    (3,4),
    (4,4),
    (5,4);
    
INSERT INTO utilisateurs (nom,prenom,type_utilisateur_id,promo_id) VALUES
	('admin','admin',3,NULL),
    ('Duchmol','Jean',2,NULL),
    ('Cidu','Léléveas',1,8);
    
INSERT INTO modules(nom,prof_id) VALUES
    ('COO',2),
    ('Algèbre',2),
    ('Web',2);

INSERT INTO creneaux (module_id,date_cours,heure_debut,heure_fin,cours_id,salle_id,promo_id,prof_id) VALUES
	(1,'2021-03-31',0900,1030,3,1,8,2),
	(1,'2021-03-31',1415,1515,2,1,8,2);
