/* ----------------------------------------------------------------------------- 
      
			Génération d'une base de données pour
					MySQL

----------------------------------------------------------------------------- */

	Projet : SAE S3.01 - Robot Championship
	Date de dernière modification : 07/01/2025

	A copier dans phpMyAdmin - dans sa propre base (ex : etu_ibezie)

/* ----------------------------------------------------------------------------- 
      TABLE : T_CHAMPIONSHIP_CHP
----------------------------------------------------------------------------- */

CREATE TABLE T_CHAMPIONSHIP_CHP (
    CHP_ID SMALLINT AUTO_INCREMENT,
    CHP_NAME CHAR(255) NOT NULL,
    CHP_DATE DATETIME NOT NULL,
    PRIMARY KEY (CHP_ID),
    UNIQUE KEY UNIQUE_T_CHAMPIONSHIP_CHP_NAME (CHP_NAME)
) ENGINE=InnoDB;

/* ----------------------------------------------------------------------------- 
      TABLE : T_TEAM_TEM
----------------------------------------------------------------------------- */

CREATE TABLE T_TEAM_TEM (
    TEM_ID SMALLINT AUTO_INCREMENT,
    USR_ID SMALLINT NOT NULL,
    TEM_NAME CHAR(255) NOT NULL,
    TEM_SCORE INT DEFAULT NULL,
    PRIMARY KEY (TEM_ID),
    UNIQUE KEY UNIQUE_T_TEAM_TEM_NAME (TEM_NAME)
) ENGINE=InnoDB;

/* ----------------------------------------------------------------------------- 
      TABLE : T_USER_USR
----------------------------------------------------------------------------- */

CREATE TABLE T_USER_USR (
    USR_ID SMALLINT AUTO_INCREMENT,
    USR_TYPE CHAR(255) NOT NULL,
    USR_MAIL CHAR(255) NOT NULL,
    USR_PASS VARCHAR(128) NOT NULL,
    PRIMARY KEY (USR_ID),
    UNIQUE KEY UNIQUE_T_USER_USR_MAIL (USR_MAIL),
    CHECK (USR_TYPE IN ('USER', 'ADMIN'))
) ENGINE=InnoDB;

/* ----------------------------------------------------------------------------- 
      TABLE : T_ENCOUNTER_ENC
----------------------------------------------------------------------------- */

CREATE TABLE T_ENCOUNTER_ENC (
    ENC_ID SMALLINT AUTO_INCREMENT,
    TEM_ID_PARTICIPATE_BLUE SMALLINT NOT NULL,
    TEM_ID_PARTICIPATE_GREEN SMALLINT NOT NULL,
    CHP_ID SMALLINT NOT NULL,
    ENC_SCORE_BLUE INT DEFAULT NULL,
    ENC_SCORE_GREEN INT DEFAULT NULL,
    ENC_STATE CHAR(255) NOT NULL,
    PRIMARY KEY (ENC_ID),
    CHECK (TEM_ID_PARTICIPATE_BLUE <> TEM_ID_PARTICIPATE_GREEN)
) ENGINE=InnoDB;

/* ----------------------------------------------------------------------------- 
      TABLE : T_MEMBER_MBR
----------------------------------------------------------------------------- */

CREATE TABLE T_MEMBER_MBR (
    MBR_ID SMALLINT AUTO_INCREMENT,
    TEM_ID SMALLINT NOT NULL,
    MBR_NAME CHAR(255) NOT NULL,
    MBR_SURNAME CHAR(255) NOT NULL,
    MBR_MAIL CHAR(255) NOT NULL,
    PRIMARY KEY (MBR_ID),
    UNIQUE KEY UNIQUE_T_MEMBER_MBR_MAIL (MBR_MAIL)
) ENGINE=InnoDB;

/* ----------------------------------------------------------------------------- 
      REFERENCES SUR LES TABLES
----------------------------------------------------------------------------- */

ALTER TABLE T_TEAM_TEM
    ADD CONSTRAINT FK_T_TEAM_TEM_T_USER_USR FOREIGN KEY (USR_ID)
    REFERENCES T_USER_USR (USR_ID);

ALTER TABLE T_ENCOUNTER_ENC
    ADD CONSTRAINT FK_T_ENCOUNTER_ENC_T_TEAM_TEM FOREIGN KEY (TEM_ID_PARTICIPATE_BLUE)
    REFERENCES T_TEAM_TEM (TEM_ID),
    ADD CONSTRAINT FK_T_ENCOUNTER_ENC_T_TEAM_TEM1 FOREIGN KEY (TEM_ID_PARTICIPATE_GREEN)
    REFERENCES T_TEAM_TEM (TEM_ID),
    ADD CONSTRAINT FK_T_ENCOUNTER_ENC_T_CHAMPIONSHIP_CHP FOREIGN KEY (CHP_ID)
    REFERENCES T_CHAMPIONSHIP_CHP (CHP_ID);

ALTER TABLE T_MEMBER_MBR
    ADD CONSTRAINT FK_T_MEMBER_MBR_T_TEAM_TEM FOREIGN KEY (TEM_ID)
    REFERENCES T_TEAM_TEM (TEM_ID);



