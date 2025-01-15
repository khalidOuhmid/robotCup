-- Insert sample competitions
INSERT INTO T_COMPETITION_CMP (CMP_ADDRESS, CMP_DATE_BEGIN, CMP_DATE_END, CMP_NAME, CMP_DESC)
VALUES ('123 Main St', '2025-01-01', '2025-01-10', 'Winter Championship', 'Annual winter event');

INSERT INTO T_COMPETITION_CMP (CMP_ADDRESS, CMP_DATE_BEGIN, CMP_DATE_END, CMP_NAME, CMP_DESC)
VALUES ('456 Elm St', '2025-02-01', '2025-02-10', 'Spring Championship', 'Annual spring event');

-- Insert sample users
INSERT INTO T_USER_USR (USR_TYPE, USR_MAIL, USR_PASS)
VALUES ('ADMIN', 'admin@example.com', 'securepassword');

INSERT INTO T_USER_USR (USR_TYPE, USR_MAIL, USR_PASS)
VALUES ('USER', 'user1@example.com', 'password123');

INSERT INTO T_USER_USR (USR_TYPE, USR_MAIL, USR_PASS)
VALUES ('USER', 'user2@example.com', 'password123');

INSERT INTO T_USER_USR (USR_TYPE, USR_MAIL, USR_PASS)
VALUES ('USER', 'user3@example.com', 'password456');

INSERT INTO T_USER_USR (USR_TYPE, USR_MAIL, USR_PASS)
VALUES ('USER', 'user4@example.com', 'password789');

INSERT INTO T_USER_USR (USR_TYPE, USR_MAIL, USR_PASS)
VALUES ('USER', 'user5@example.com', 'passwordabc');

INSERT INTO T_USER_USR (USR_TYPE, USR_MAIL, USR_PASS)
VALUES ('USER', 'user6@example.com', 'passworddef');

INSERT INTO T_USER_USR (USR_TYPE, USR_MAIL, USR_PASS)
VALUES ('USER', 'user7@example.com', 'passwordghi');

-- Insert sample teams
INSERT INTO T_TEAM_TEM (USR_ID, TEM_NAME, CMP_ID)
VALUES (2, 'Blue Warriors', 1);

INSERT INTO T_TEAM_TEM (USR_ID, TEM_NAME, CMP_ID)
VALUES (3, 'Green Giants', 2);

INSERT INTO T_TEAM_TEM (USR_ID, TEM_NAME, CMP_ID)
VALUES (4, 'Red Dragons', 1);

INSERT INTO T_TEAM_TEM (USR_ID, TEM_NAME, CMP_ID)
VALUES (5, 'Yellow Falcons', 1);

INSERT INTO T_TEAM_TEM (USR_ID, TEM_NAME, CMP_ID)
VALUES (6, 'Purple Panthers', 2);

INSERT INTO T_TEAM_TEM (USR_ID, TEM_NAME, CMP_ID)
VALUES (7, 'Black Hawks', 2);

INSERT INTO T_TEAM_TEM (USR_ID, TEM_NAME, CMP_ID)
VALUES (8, 'White Wolves', 1);

-- Insert sample fields
INSERT INTO T_FIELD_FLD (CMP_ID, FLD_NAME)
VALUES (1, 'Field A');

INSERT INTO T_FIELD_FLD (CMP_ID, FLD_NAME)
VALUES (1, 'Field B');

INSERT INTO T_FIELD_FLD (CMP_ID, FLD_NAME)
VALUES (2, 'Field C');

INSERT INTO T_FIELD_FLD (CMP_ID, FLD_NAME)
VALUES (2, 'Field D');

INSERT INTO T_FIELD_FLD (CMP_ID, FLD_NAME)
VALUES (1, 'Field E');

-- Insert sample members
INSERT INTO T_MEMBER_MBR (TEM_ID, MBR_NAME, MBR_SURNAME, MBR_MAIL)
VALUES (1, 'John', 'Doe', 'john.doe@example.com');

INSERT INTO T_MEMBER_MBR (TEM_ID, MBR_NAME, MBR_SURNAME, MBR_MAIL)
VALUES (2, 'Jane', 'Smith', 'jane.smith@example.com');

-- Insert sample championships
INSERT INTO T_CHAMPIONSHIP_CHP (CMP_ID)
VALUES (1);

-- Insert sample tournaments
INSERT INTO T_TOURNAMENT_TNM (CMP_ID)
VALUES (1);

INSERT INTO T_TIMESLOT_SLT (SLT_DATE_BEGIN, SLT_DATE_END)
VALUES ('2025-01-07 10:00:00', '2025-01-07 12:00:00'),
('2025-01-08 14:00:00', '2025-01-08 16:00:00'),
('2025-01-09 10:00:00', '2025-01-09 12:00:00'),
('2025-01-11 10:00:00', '2025-01-11 12:00:00'),
('2025-01-12 14:00:00', '2025-01-12 16:00:00'),
('2025-01-13 10:00:00', '2025-01-13 12:00:00'),
('2025-01-14 10:00:00', '2025-01-14 12:00:00'),
('2025-01-15 10:00:00', '2025-01-15 12:00:00');

-- Insert sample encounters
INSERT INTO T_ENCOUNTER_ENC (CHP_ID, FLD_ID, TEM_ID_BLUE, TEM_ID_GREEN, ENC_STATE, SLT_ID, ENC_SCORE_BLUE, ENC_SCORE_GREEN, ENC_PENALTY_BLUE, ENC_PENALTY_GREEN)
VALUES ( 1, 1, 1, 2, 'CONCLUE', 1, 15, 12, FALSE, FALSE);

INSERT INTO T_ENCOUNTER_ENC (CHP_ID, FLD_ID, TEM_ID_BLUE, TEM_ID_GREEN, ENC_STATE, SLT_ID, ENC_SCORE_BLUE, ENC_SCORE_GREEN, ENC_PENALTY_BLUE, ENC_PENALTY_GREEN)
VALUES ( 1, 2, 3, 4, 'CONCLUE', 2, 12, 12, FALSE, FALSE);

INSERT INTO T_ENCOUNTER_ENC (CHP_ID, FLD_ID, TEM_ID_BLUE, TEM_ID_GREEN, ENC_STATE, SLT_ID, ENC_SCORE_BLUE, ENC_SCORE_GREEN, ENC_PENALTY_BLUE, ENC_PENALTY_GREEN)
VALUES ( 1, 3, 5, 6, 'CONCLUE', 3, 5, 6, FALSE, FALSE);

INSERT INTO T_ENCOUNTER_ENC (CHP_ID, FLD_ID, TEM_ID_BLUE, TEM_ID_GREEN, ENC_STATE, SLT_ID, ENC_SCORE_BLUE, ENC_SCORE_GREEN, ENC_PENALTY_BLUE, ENC_PENALTY_GREEN)
VALUES ( 1, 5, 1, 3, 'CONCLUE', 4, 4, 7, FALSE, FALSE);

INSERT INTO T_ENCOUNTER_ENC (CHP_ID, FLD_ID, TEM_ID_BLUE, TEM_ID_GREEN, ENC_STATE, SLT_ID, ENC_SCORE_BLUE, ENC_SCORE_GREEN, ENC_PENALTY_BLUE, ENC_PENALTY_GREEN)
VALUES ( 1, 1, 4, 6, 'CONCLUE', 5, 1, 1, FALSE, FALSE);

INSERT INTO T_ENCOUNTER_ENC (CHP_ID, FLD_ID, TEM_ID_BLUE, TEM_ID_GREEN, ENC_STATE, SLT_ID, ENC_SCORE_BLUE, ENC_SCORE_GREEN, ENC_PENALTY_BLUE, ENC_PENALTY_GREEN)
VALUES ( 1, 2, 5, 7, 'CONCLUE', 6, 3, 2, FALSE, FALSE);

INSERT INTO T_ENCOUNTER_ENC (CHP_ID, FLD_ID, TEM_ID_BLUE, TEM_ID_GREEN, ENC_STATE, SLT_ID, ENC_PENALTY_BLUE, ENC_PENALTY_GREEN)
VALUES ( 1, 2, 5, 7, 'FORFAIT', 7, TRUE, FALSE);

INSERT INTO T_ENCOUNTER_ENC (CHP_ID, FLD_ID, TEM_ID_BLUE, TEM_ID_GREEN, ENC_STATE, SLT_ID, ENC_SCORE_BLUE, ENC_SCORE_GREEN, ENC_PENALTY_BLUE, ENC_PENALTY_GREEN)
VALUES ( 1, 2, 5, 3, 'CONCLUE', 8, 6, 2, FALSE, FALSE);
