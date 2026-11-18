-- Deefy : création et remplissage de la base de données
-- Import : mysql -u USER -p NOM_BASE < sql/deefy.sql
-- (sur webetu, importer ce fichier dans votre base via phpMyAdmin)

DROP TABLE IF EXISTS playlist2track;
DROP TABLE IF EXISTS track;
DROP TABLE IF EXISTS playlist;

CREATE TABLE playlist (
    id  INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE track (
    id       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    titre    VARCHAR(150) NOT NULL,
    artiste  VARCHAR(150) NOT NULL,
    album    VARCHAR(150) DEFAULT NULL,
    annee    SMALLINT UNSIGNED DEFAULT NULL,
    genre    VARCHAR(50) DEFAULT NULL,
    duree    INT UNSIGNED NOT NULL,
    filename VARCHAR(64) DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE playlist2track (
    id_pl               INT UNSIGNED NOT NULL,
    id_track            INT UNSIGNED NOT NULL,
    no_piste_dans_liste INT UNSIGNED NOT NULL,
    PRIMARY KEY (id_pl, id_track),
    CONSTRAINT fk_p2t_playlist FOREIGN KEY (id_pl) REFERENCES playlist (id) ON DELETE CASCADE,
    CONSTRAINT fk_p2t_track    FOREIGN KEY (id_track) REFERENCES track (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Données de test
INSERT INTO playlist (id, nom) VALUES
    (1, 'Classiques du rock'),
    (2, 'Soirée chill'),
    (3, 'Playlist vide');

INSERT INTO track (id, titre, artiste, album, annee, genre, duree, filename) VALUES
    (1, 'Bohemian Rhapsody', 'Queen', 'A Night at the Opera', 1975, 'Rock', 354, NULL),
    (2, 'Hotel California', 'Eagles', 'Hotel California', 1976, 'Rock', 391, NULL),
    (3, 'Stairway to Heaven', 'Led Zeppelin', 'Led Zeppelin IV', 1971, 'Rock', 482, NULL),
    (4, 'Weightless', 'Marconi Union', 'Weightless', 2011, 'Ambient', 480, NULL),
    (5, 'Gymnopédie n°1', 'Erik Satie', 'Gymnopédies', 1888, 'Classique', 180, NULL);

INSERT INTO playlist2track (id_pl, id_track, no_piste_dans_liste) VALUES
    (1, 1, 1), (1, 2, 2), (1, 3, 3),
    (2, 4, 1), (2, 5, 2);
