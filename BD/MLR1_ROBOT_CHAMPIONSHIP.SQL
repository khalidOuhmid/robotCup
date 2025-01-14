/* ----------------------------------------------------------------------------- 
      
			Generation d'une base de donnees pour
					MySQL



	Projet : SAE S3.01 - Robot Championship
	Date de derniere modification : 14/01/2025
	Auteur : Isadora Bezie

	A copier dans phpMyAdmin - dans sa propre base (ex : etu_ibezie)


----------------------------------------------------------------------------- */

-- ---------------------------------------------------------------------------
--      TABLE : T_COMPETITION_CMP
-- ---------------------------------------------------------------------------

CREATE TABLE T_COMPETITION_CMP (
    CMP_ID SMALLINT AUTO_INCREMENT,
    CMP_ADDRESS CHAR(255) DEFAULT NULL,
    CMP_DATE_BEGIN DATETIME NOT NULL,
    CMP_DATE_END DATETIME NOT NULL,
    CMP_NAME CHAR(32) NOT NULL,
    CMP_DESC CHAR(255) DEFAULT NULL,
    CMP_ROUNDS SMALLINT DEFAULT NULL,
    CMP_RND_SYSTEM CHAR(32) DEFAULT NULL,
    PRIMARY KEY (CMP_ID),
    CONSTRAINT CHK_CMP_RND_SYSTEM CHECK (
        ( CMP_ROUNDS IS NULL AND CMP_RND_SYSTEM IS NULL) OR
        ( CMP_RND_SYSTEM IN ('HOLLANDAIS', 'MONRAD', 'SUISSE'))
    ),
    CONSTRAINT CHK_CMP_DATES CHECK (CMP_DATE_BEGIN < CMP_DATE_END),
    CONSTRAINT UQ_CMP_NAME UNIQUE (CMP_NAME)
);

-- ---------------------------------------------------------------------------
--      TABLE : T_CHAMPIONSHIP_CHP
-- ---------------------------------------------------------------------------

CREATE TABLE T_CHAMPIONSHIP_CHP (
    CHP_ID SMALLINT AUTO_INCREMENT,
    CMP_ID SMALLINT NOT NULL,
    PRIMARY KEY (CHP_ID)
);

-- ---------------------------------------------------------------------------
--      TABLE : T_TIMESLOT_SLT
-- ---------------------------------------------------------------------------

CREATE TABLE T_TIMESLOT_SLT (
    SLT_ID SMALLINT AUTO_INCREMENT,
    SLT_DATE_BEGIN DATETIME NOT NULL,
    SLT_DATE_END DATETIME NOT NULL,
    PRIMARY KEY (SLT_ID),
    CONSTRAINT CHK_TIMESLOT_DATE CHECK (SLT_DATE_BEGIN < SLT_DATE_END)
);

-- ---------------------------------------------------------------------------
--      TABLE : T_TOURNAMENT_TNM
-- ---------------------------------------------------------------------------

CREATE TABLE T_TOURNAMENT_TNM (
    TNM_ID SMALLINT AUTO_INCREMENT,
    CMP_ID SMALLINT NOT NULL,
    PRIMARY KEY (TNM_ID)
);

-- ---------------------------------------------------------------------------
--      TABLE : T_FIELD_FLD
-- ---------------------------------------------------------------------------

CREATE TABLE T_FIELD_FLD (
    FLD_ID SMALLINT AUTO_INCREMENT,
    CMP_ID SMALLINT NOT NULL,
    FLD_NAME CHAR(32) NOT NULL,
    PRIMARY KEY (FLD_ID),
    CONSTRAINT UQ_FLD_NAME UNIQUE (FLD_NAME)
);

-- ---------------------------------------------------------------------------
--      TABLE : T_USER_USR
-- ---------------------------------------------------------------------------

CREATE TABLE T_USER_USR (
    USR_ID SMALLINT AUTO_INCREMENT,
    USR_TYPE CHAR(255) NOT NULL,
    USR_MAIL CHAR(255) NOT NULL,
    USR_PASS VARCHAR(128) NOT NULL,
    USR_CREATION_DATE DATETIME NOT NULL,
    PRIMARY KEY (USR_ID),
    CONSTRAINT UQ_USR_MAIL UNIQUE (USR_MAIL),
    CONSTRAINT CHK_USR_TYPE CHECK (
        USR_TYPE IN ('USER', 'ADMIN', 'ADMINISTRATOR')
    )
);


-- ---------------------------------------------------------------------------
--      TABLE : T_TEAM_TEM
-- ---------------------------------------------------------------------------

CREATE TABLE T_TEAM_TEM (
    TEM_ID SMALLINT AUTO_INCREMENT,
    USR_ID SMALLINT NOT NULL,
    CMP_ID SMALLINT NOT NULL,
    TEM_NAME CHAR(32) NOT NULL,
    TEM_CREATION_DATE DATETIME NOT NULL,
    TEM_STRUCT CHAR(32) DEFAULT NULL,
    PRIMARY KEY (TEM_ID),
    CONSTRAINT UQ_TEM_NAME_CMP_ID UNIQUE (TEM_NAME, CMP_ID),
    CONSTRAINT UQ_USR_ID_CMP_ID UNIQUE (USR_ID, CMP_ID)
);

-- ---------------------------------------------------------------------------
--      TABLE : T_ENCOUNTER_ENC
-- ---------------------------------------------------------------------------

CREATE TABLE T_ENCOUNTER_ENC (
    ENC_ID SMALLINT AUTO_INCREMENT,
    TNM_ID SMALLINT DEFAULT NULL,
    TEM_ID_BLUE SMALLINT DEFAULT NULL,
    TEM_ID_GREEN SMALLINT DEFAULT NULL,
    CHP_ID SMALLINT DEFAULT NULL,
    TEM_ID_REFEREE SMALLINT DEFAULT NULL,
    FLD_ID SMALLINT NOT NULL,
    SLT_ID SMALLINT NOT NULL,
    ENC_STATE CHAR(255) NOT NULL,
    ENC_SCORE_BLUE SMALLINT DEFAULT NULL,
    ENC_SCORE_GREEN SMALLINT DEFAULT NULL,
    ENC_PENALTY_BLUE BOOLEAN NOT NULL,
    ENC_PENALTY_GREEN BOOLEAN NOT NULL,
    ENC_COMMENT CHAR(255) DEFAULT NULL,
    PRIMARY KEY (ENC_ID),
    CONSTRAINT CHK_ENC_SCORE CHECK (
        ( ENC_SCORE_BLUE >= 0 OR ENC_SCORE_BLUE IS NULL) AND 
        ( ENC_SCORE_GREEN >= 0 OR ENC_SCORE_GREEN IS NULL)
    ),
    CONSTRAINT CHK_ENC_STATE CHECK (
        ENC_STATE IN ('PROGRAMMEE', 'CONCLUE', 'EN COURS', 'FORFAIT', 'ANNULEE')
    ),
    CONSTRAINT CHK_ENC_STATE_CONCLUE CHECK (
        (ENC_STATE = 'CONCLUE' AND ENC_SCORE_BLUE IS NOT NULL AND ENC_SCORE_GREEN IS NOT NULL) OR
        (ENC_STATE <> 'CONCLUE')
    ),
    CONSTRAINT CHK_ENC_STATE_FORFAIT CHECK (
        (ENC_STATE = 'FORFAIT' AND ENC_SCORE_BLUE IS NULL AND ENC_SCORE_GREEN IS NULL)
        AND (ENC_PENALTY_BLUE IS TRUE OR ENC_PENALTY_GREEN IS TRUE)
    ),
    CONSTRAINT CHK_ENC_STATE_NULL CHECK (
        (ENC_STATE = 'PROGRAMMEE' AND ENC_SCORE_BLUE IS NULL AND ENC_SCORE_GREEN IS NULL) OR
        (ENC_STATE = 'ANNULEE' AND ENC_SCORE_BLUE IS NULL AND ENC_SCORE_GREEN IS NULL)
    ),
    CONSTRAINT CHK_ENC_TEAMS_DIFFERENT CHECK (
        TEM_ID_GREEN <> TEM_ID_REFEREE AND
        TEM_ID_GREEN <> TEM_ID_BLUE AND
        TEM_ID_BLUE <> TEM_ID_REFEREE
    ),
    CONSTRAINT UQ_TEM_ID_REFEREE_SLT_ID UNIQUE (TEM_ID_REFEREE, SLT_ID),
    CONSTRAINT UQ_SLT_ID_FLD_ID UNIQUE (SLT_ID, FLD_ID),
    CONSTRAINT UQ_TEM_ID_BLUE_SLT_ID_FLD_ID UNIQUE (TEM_ID_BLUE, TEM_ID_GREEN, SLT_ID),
    CONSTRAINT UQ_TEM_ID_BLUE_SLT_ID UNIQUE (TEM_ID_BLUE, SLT_ID),
    CONSTRAINT UQ_TEM_ID_GREEN_SLT_ID UNIQUE (TEM_ID_GREEN, SLT_ID)
);

-- ---------------------------------------------------------------------------
--      TABLE : T_MEMBER_MBR
-- ---------------------------------------------------------------------------

CREATE TABLE T_MEMBER_MBR (
    MBR_ID SMALLINT AUTO_INCREMENT,
    TEM_ID SMALLINT NOT NULL,
    MBR_NAME CHAR(32) NOT NULL,
    MBR_SURNAME CHAR(32) NOT NULL,
    MBR_MAIL CHAR(255) NOT NULL,
    PRIMARY KEY (MBR_ID),
    CONSTRAINT UQ_MBR_MAIL UNIQUE (MBR_MAIL)
);

-- ---------------------------------------------------------------------------
--      REFERENCES SUR LES TABLES
-- ---------------------------------------------------------------------------

ALTER TABLE T_CHAMPIONSHIP_CHP 
    ADD CONSTRAINT FK_T_CHAMPIONSHIP_CHP_T_COMPETITION_CMP FOREIGN KEY (CMP_ID) 
    REFERENCES T_COMPETITION_CMP (CMP_ID);

ALTER TABLE T_TEAM_TEM 
    ADD CONSTRAINT FK_T_TEAM_TEM_T_USER_USR FOREIGN KEY (USR_ID) 
    REFERENCES T_USER_USR (USR_ID);

ALTER TABLE T_TOURNAMENT_TNM 
    ADD CONSTRAINT FK_T_TOURNAMENT_TNM_T_COMPETITION_CMP FOREIGN KEY (CMP_ID) 
    REFERENCES T_COMPETITION_CMP (CMP_ID);

ALTER TABLE T_FIELD_FLD 
    ADD CONSTRAINT FK_T_FIELD_FLD_T_COMPETITION_CMP FOREIGN KEY (CMP_ID) 
    REFERENCES T_COMPETITION_CMP (CMP_ID);

ALTER TABLE T_ENCOUNTER_ENC 
    ADD CONSTRAINT FK_T_ENCOUNTER_ENC_T_TOURNAMENT_TNM FOREIGN KEY (TNM_ID) 
    REFERENCES T_TOURNAMENT_TNM (TNM_ID);

ALTER TABLE T_ENCOUNTER_ENC 
    ADD CONSTRAINT FK_T_ENCOUNTER_ENC_T_TEAM_TEM FOREIGN KEY (TEM_ID_BLUE) 
    REFERENCES T_TEAM_TEM (TEM_ID);

ALTER TABLE T_ENCOUNTER_ENC 
    ADD CONSTRAINT FK_T_ENCOUNTER_ENC_T_CHAMPIONSHIP_CHP FOREIGN KEY (CHP_ID) 
    REFERENCES T_CHAMPIONSHIP_CHP (CHP_ID);

ALTER TABLE T_ENCOUNTER_ENC 
    ADD CONSTRAINT FK_T_ENCOUNTER_ENC_T_TEAM_TEM1 FOREIGN KEY (TEM_ID_REFEREE) 
    REFERENCES T_TEAM_TEM (TEM_ID);

ALTER TABLE T_ENCOUNTER_ENC 
    ADD CONSTRAINT FK_T_ENCOUNTER_ENC_T_FIELD_FLD FOREIGN KEY (FLD_ID) 
    REFERENCES T_FIELD_FLD (FLD_ID);

ALTER TABLE T_ENCOUNTER_ENC 
    ADD CONSTRAINT FK_T_ENCOUNTER_ENC_T_TEAM_TEM2 FOREIGN KEY (TEM_ID_GREEN) 
    REFERENCES T_TEAM_TEM (TEM_ID);

ALTER TABLE T_ENCOUNTER_ENC 
    ADD CONSTRAINT FK_T_ENCOUNTER_ENC_T_TIMESLOT_SLT FOREIGN KEY (SLT_ID) 
    REFERENCES T_TIMESLOT_SLT (SLT_ID);

ALTER TABLE T_MEMBER_MBR 
    ADD CONSTRAINT FK_T_MEMBER_MBR_T_TEAM_TEM FOREIGN KEY (TEM_ID) 
    REFERENCES T_TEAM_TEM (TEM_ID);

ALTER TABLE T_TEAM_TEM 
    ADD CONSTRAINT FK_T_TEAM_TEM_T_COMPETITION_CMP FOREIGN KEY (CMP_ID) 
    REFERENCES T_COMPETITION_CMP (CMP_ID);

-- ---------------------------------------------------------------------------
--               FIN DE GENERATION
-- ---------------------------------------------------------------------------


-- Trigger on inserting an encounter with the same field and overlapping dates and same teams and overlapping dates
DELIMITER //

CREATE TRIGGER trg_prevent_overlap_encounter
BEFORE INSERT ON T_ENCOUNTER_ENC
FOR EACH ROW
BEGIN
    DECLARE field_overlap_count INT;
    DECLARE team_overlap_count INT;

    -- Check for overlapping encounters in the same field
    SELECT COUNT(*)
    INTO field_overlap_count
    FROM T_ENCOUNTER_ENC
    WHERE FLD_ID = NEW.FLD_ID
      AND (NEW.ENC_DATE_BEGIN BETWEEN ENC_DATE_BEGIN AND ENC_DATE_END
           OR NEW.ENC_DATE_END BETWEEN ENC_DATE_BEGIN AND ENC_DATE_END
           OR ENC_DATE_BEGIN BETWEEN NEW.ENC_DATE_BEGIN AND NEW.ENC_DATE_END
           OR ENC_DATE_END BETWEEN NEW.ENC_DATE_BEGIN AND NEW.ENC_DATE_END);

    -- Check for overlapping encounters with the same teams (either blue or green)
    SELECT COUNT(*)
    INTO team_overlap_count
    FROM T_ENCOUNTER_ENC
    WHERE (TEM_ID_BLUE = NEW.TEM_ID_BLUE OR TEM_ID_GREEN = NEW.TEM_ID_GREEN
           OR TEM_ID_BLUE = NEW.TEM_ID_GREEN OR TEM_ID_GREEN = NEW.TEM_ID_BLUE)
      AND (NEW.ENC_DATE_BEGIN BETWEEN ENC_DATE_BEGIN AND ENC_DATE_END
           OR NEW.ENC_DATE_END BETWEEN ENC_DATE_BEGIN AND ENC_DATE_END
           OR ENC_DATE_BEGIN BETWEEN NEW.ENC_DATE_BEGIN AND NEW.ENC_DATE_END
           OR ENC_DATE_END BETWEEN NEW.ENC_DATE_BEGIN AND NEW.ENC_DATE_END);

    -- If any overlap is detected, raise an error
    IF field_overlap_count > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot insert encounter: overlapping dates in the same field.';
    ELSEIF team_overlap_count > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot insert encounter: overlapping dates with the same teams.';
    END IF;
END;
//

DELIMITER ;


-- Trigger on inserting a competition with overlapping dates
DELIMITER //

CREATE TRIGGER trg_prevent_overlap_competition
BEFORE INSERT ON T_COMPETITION_CMP
FOR EACH ROW
BEGIN
    DECLARE overlap_count INT;

    -- Check for overlapping competitions
    SELECT COUNT(*)
    INTO overlap_count
    FROM T_COMPETITION_CMP
    WHERE (NEW.CMP_DATE_BEGIN BETWEEN CMP_DATE_BEGIN AND CMP_DATE_END)
       OR (NEW.CMP_DATE_END BETWEEN CMP_DATE_BEGIN AND CMP_DATE_END)
       OR (CMP_DATE_BEGIN BETWEEN NEW.CMP_DATE_BEGIN AND NEW.CMP_DATE_END)
       OR (CMP_DATE_END BETWEEN NEW.CMP_DATE_BEGIN AND NEW.CMP_DATE_END);

    -- If overlap exists, raise an error
    IF overlap_count > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot insert competition: overlapping dates with an existing competition.';
    END IF;
END;
//

DELIMITER ;


-- Trigger for a maximum of 16 encounters in a tournament
DELIMITER //

CREATE TRIGGER trg_max_encounters_per_tournament
BEFORE INSERT ON T_ENCOUNTER_ENC
FOR EACH ROW
BEGIN
    DECLARE encounter_count INT;

    -- Count the number of encounters already in the same tournament
    SELECT COUNT(*)
    INTO encounter_count
    FROM T_ENCOUNTER_ENC
    WHERE TNM_ID = NEW.TNM_ID;

    -- Check if the count exceeds the limit of 16
    IF encounter_count >= 16 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot insert encounter: a tournament cannot have more than 16 encounters.';
    END IF;
END;
//

DELIMITER ;

CREATE OR REPLACE VIEW team_stats_with_forfeit AS
SELECT 
    base.team_id,
    base.team_name,
    IFNULL(base.total_matches, 0) AS total_matches,
    IFNULL(base.total_wins, 0) AS total_wins,
    IFNULL(base.total_draws, 0) AS total_draws,
    IFNULL(base.total_losses, 0) AS total_losses,
    IFNULL(base.total_points, 0) AS total_points,
    IFNULL(base.average_score, 0) AS average_score,
    RANK() OVER (ORDER BY IFNULL(base.total_points, 0) DESC, IFNULL(base.average_score, 0) DESC) AS rank
FROM 
    (SELECT 
        T.TEM_ID AS team_id,
        T.TEM_NAME AS team_name,
        COUNT(E.ENC_ID) AS total_matches,
        SUM(
            CASE 
                WHEN (E.TEM_ID_BLUE = T.TEM_ID AND E.ENC_SCORE_BLUE > E.ENC_SCORE_GREEN) OR 
                     (E.TEM_ID_GREEN = T.TEM_ID AND E.ENC_SCORE_GREEN > E.ENC_SCORE_BLUE) 
                THEN 1 ELSE 0 
            END
        ) AS total_wins,
        SUM(
            CASE 
                WHEN E.ENC_SCORE_BLUE = E.ENC_SCORE_GREEN THEN 1 ELSE 0 
            END
        ) AS total_draws,
        SUM(
            CASE 
                WHEN (E.TEM_ID_BLUE = T.TEM_ID AND E.ENC_SCORE_BLUE < E.ENC_SCORE_GREEN) OR 
                     (E.TEM_ID_GREEN = T.TEM_ID AND E.ENC_SCORE_GREEN < E.ENC_SCORE_BLUE) 
                THEN 1 ELSE 0 
            END
        ) AS total_losses,
        SUM(
            CASE 
                WHEN (E.TEM_ID_BLUE = T.TEM_ID AND E.ENC_SCORE_BLUE = -1) OR 
                     (E.TEM_ID_GREEN = T.TEM_ID AND E.ENC_SCORE_GREEN = -1) 
                THEN -1
                ELSE 
                    CASE 
                        WHEN (E.TEM_ID_BLUE = T.TEM_ID AND E.ENC_SCORE_BLUE > E.ENC_SCORE_GREEN) OR 
                             (E.TEM_ID_GREEN = T.TEM_ID AND E.ENC_SCORE_GREEN > E.ENC_SCORE_BLUE) 
                        THEN 3
                        WHEN E.ENC_SCORE_BLUE = E.ENC_SCORE_GREEN THEN 1
                        ELSE 0
                    END
            END
        ) AS total_points,
        AVG(
            CASE 
                WHEN E.TEM_ID_BLUE = T.TEM_ID THEN E.ENC_SCORE_BLUE
                WHEN E.TEM_ID_GREEN = T.TEM_ID THEN E.ENC_SCORE_GREEN
                ELSE NULL
            END
        ) AS average_score
    FROM T_TEAM_TEM T
    LEFT JOIN T_ENCOUNTER_ENC E 
        ON T.TEM_ID = E.TEM_ID_BLUE OR T.TEM_ID = E.TEM_ID_GREEN
    GROUP BY T.TEM_ID, T.TEM_NAME
    ) AS base;

