DROP type IF EXISTS zone_prestock;
DROP type IF EXISTS categ;
DROP TABLE IF EXISTS produit;
DROP TABLE IF EXISTS Livraison;
DROP TABLE IF EXISTS Palette;
DROP TABLE IF EXISTS Facture;
DROP TABLE IF EXISTS Fournisseur;
DROP TABLE IF EXISTS Manutentionnaire;
DROP TABLE IF EXISTS Caissier;
DROP TABLE IF EXISTS Manager;
DROP TABLE IF EXISTS Receptionnaire;
DROP TABLE IF EXISTS Employe;

-- TABLE EMPLOYE ET SES DESCENDANTS
CREATE TABLE Employe(
    id_employe CHAR(6) PRIMARY KEY,
    nom varchar(50) NOT NULL,
    prenom varchar(50) NOT NULL,
    num_tel char(10) NOT NULL,
    id_responsable CHAR(6),
     -- CONTRAINTES
    CONSTRAINT FK_Employe_Responsable FOREIGN KEY (id_responsable) REFERENCES Employe(id_employe),
    CHECK (LENGTH(nom) >= 2),
    CHECK (LENGTH(prenom) >= 2),
    CHECK (num_tel LIKE '0%')
);
CREATE TABLE Receptionnaire(
    id_receptionnaire CHAR(6) PRIMARY KEY,
    quai_de_reception smallint,
    -- CONTRAINTES
    CONSTRAINT FK_Receptionnaire_Emploaye FOREIGN KEY (id_receptionnaire) REFERENCES Employe(id_employe)
);
CREATE TABLE Manager(
    id_manager CHAR(6) PRIMARY KEY,
    num_bureau smallint NOT NULL,
    -- CONTRAINTES
    CONSTRAINT FK_Manager_Employe FOREIGN KEY (id_manager) REFERENCES Employe(id_employe)
);
CREATE TABLE Caissier(
    id_caissier CHAR(6) PRIMARY KEY,
    num_caisse smallint NOT NULL,
    -- CONTRAINTES
    CONSTRAINT FK_Caissier_Employe FOREIGN KEY (id_caissier) REFERENCES Employe(id_employe)
);

CREATE TABLE Manutentionnaire(
    id_manutentionnaire CHAR(6) PRIMARY KEY,
    num_equipe smallint NOT NULL,
    -- CONTRAINTES
    CONSTRAINT FK_Manager_Employe  FOREIGN KEY (id_manutentionnaire) REFERENCES Employe(id_employe)
);



-- TABLES RELATIVES A LA COMMANDE, LIVRAISON ET RECEPTION 
CREATE TABLE Fournisseur(
    id_fournisseur CHAR(10) PRIMARY KEY,
    nom_fournissur VARCHAR(50) NOT NULL,
    num_tel_fournisseur CHAR(10) NOT NULL,
    email_fournisseur VARCHAR(50) NOT NULL,
    -- CONTRAINTES
    CHECK (num_tel_fournisseur LIKE '0%')
);
CREATE TYPE zone_prestock AS ENUM('zoneA','zoneB','zoneC');
CREATE TABLE Palette(
    id_palette VARCHAR(100) PRIMARY KEY,
    est_endommage BOOLEAN NOT NULL,
    zone_pre_stockage VARCHAR(10) NOT NULL,
    id_receptionnaire CHAR(10),
    id_livraison VARCHAR(100),
    -- CONTRAINTES 
    CONSTRAINT FK_Palette_Receptionnaire FOREIGN KEY (id_receptionnaire) REFERENCES Receptionnaire(id_receptionnaire),
    CONSTRAINT FK_Palette_Manutentionnaire FOREIGN KEY (id_manutentionnaire) REFERENCES Manutentionnaire(id_manutentionnaire)
);
CREATE TABLE Livraison(
    id_livraison VARCHAR(100) PRIMARY KEY,
    date_reception DATE NULL,
    date_envoie DATE NOT NULL,
    date_reception_prevue DATE NOT NULL,
    id_manager CHAR(10),
    id_commande VARCHAR (10),
    -- CONTRAINTES 
    CONSTRAINT FK_Livraison_Manager FOREIGN KEY (id_manager) REFERENCES Manager(id_manager),
    CONSTRAINT FK_Livraison_Commande FOREIGN KEY (id_commande) REFERENCES Commande(id_commande)
);
-- TABLES PRODUIT ET TYPE PRODUIT
CREATE TYPE categ AS ENUM ('Electromenager', 'Outillage', 'Meuble');
CREATE TABLE produit(
    id_produit CHAR(10) PRIMARY KEY,
    nom VARCHAR(20) NOT NULL,
    description VARCHAR(2000),
    prix FLOAT NOT NULL,
    allee smallint NOT NULL,
    emplacement smallint NOT NULL,
    longueur FLOAT,
    largeur FLOAT,
    couleur VARCHAR(100),
    poids FLOAT,
    categorie categ,

    stock int,
    pays VARCHAR(20),

    -- CONTRAINTES
    CHECK (LENGTH(nom) >= 2)
);


CREATE TABLE Commande(
    id_commande CHAR(12),
    date_commande timestamp
);

CREATE TABLE Satisfait(
    id_produit CHAR(10),
    id_fournisseur CHAR(10),
    id_commande CHAR(12),
    nb_produits int,
    -- CONTRAINTES 
    CONSTRAINT FK_Satisfait_produit FOREIGN KEY (id_produit) REFERENCES produit(id_produit),
    CONSTRAINT FK_Satisfait_fournisseur FOREIGN KEY (id_fournisseur) REFERENCES Fournisseur(id_fournisseur),
    CONSTRAINT FK_Satisfait_commande FOREIGN KEY (id_commande) REFERENCES Commande(id_commande),
    CONSTRAINT PK_Satisfait PRIMARY KEY(id_produit,id_fournisseur,id_commande)
);

CREATE TABLE Contient(
    id_palette VARCHAR(100),
    id_produit CHAR(10),
    nb_exemp_contenu int,
    -- CONTRAINTES 
    CONSTRAINT FK_Contient_Palette FOREIGN KEY (id_palette) REFERENCES Palette(id_palette),
    CONSTRAINT FK_Contient_produit FOREIGN KEY (id_produit) REFERENCES produit(id_produit),
    CONSTRAINT PK_Contient PRIMARY KEY(id_palette,id_produit)
);

CREATE TABLE Vend(
    id_caissier CHAR(10),
    id_produit CHAR(10),
    nb_exemp_vendu int,
    -- CONTRAINTES 
    CONSTRAINT FK_Vend_Caissier FOREIGN KEY (id_caissier) REFERENCES Caissier(id_caissier),
    CONSTRAINT FK_Vend_produit FOREIGN KEY (id_produit) REFERENCES produit(id_produit),
    CONSTRAINT PK_Vend PRIMARY KEY(id_caissier,id_produit)
);

CREATE TABLE Stocke(
    id_manutentionnaire CHAR(6),
    id_palette VARCHAR(100),
    -- CONTRAINTES 
    CONSTRAINT FK_Stocke_manutentionnaire FOREIGN KEY (id_manutentionnaire) REFERENCES Manutentionnaire(id_manutentionnaire) ON DELETE CASCADE,
    CONSTRAINT FK_Stocke_Palette FOREIGN KEY (id_palette) REFERENCES Palette(id_palette) ON DELETE CASCADE,
    CONSTRAINT PK_Stocke PRIMARY KEY(id_manutentionnaire,id_palette)
);