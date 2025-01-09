/*Script de remplissage*/

/*Insertion des championnats*/
INSERT INTO T_CHAMPIONSHIP_CHP (CHP_NAME, CHP_DATE) 
VALUES 
    ('Championship 2025', '2025-01-01'),
    ('Championship 2026', '2026-01-01');  
    
/*Insertion des utilisateurs*/
INSERT INTO T_USER_USR (USR_TYPE, USR_MAIL, USR_PASS) 
VALUES 
    ('USER', 'user1@example.com', 'password1'), 
    ('USER', 'user2@example.com', 'password2'),
    ('ADMIN', 'admin1@example.com', 'password3'),
    ('USER', 'user3@example.com', 'password4');
      
/*Insertion des equipes*/
INSERT INTO T_TEAM_TEM (TEM_NAME, TEM_SCORE, USR_ID) 
VALUES 
    ('Team A', 3, 
    (SELECT USR_ID FROM T_USER_USR WHERE USR_MAIL LIKE 'user1@example.com')),
    ('Team B', 1,
    (SELECT USR_ID FROM T_USER_USR WHERE USR_MAIL LIKE 'user2@example.com')),
    ('Team C', NULL,
    (SELECT USR_ID FROM T_USER_USR WHERE USR_MAIL LIKE 'user3@example.com'));


/*Insertion des membres*/
INSERT INTO T_MEMBER_MBR (MBR_NAME, MBR_SURNAME, MBR_MAIL, TEM_ID)
VALUES
    ('John', 'Dunne', 'john-d@example.com', 
    (SELECT TEM_ID FROM T_TEAM_TEM WHERE TEM_NAME LIKE 'Team A')),
    ('Jane', 'Doe', 'jane-d@example.com', 
    (SELECT TEM_ID FROM T_TEAM_TEM WHERE TEM_NAME LIKE 'Team A')),
    ('Jeff', 'Dry', 'jeff-d@example.com', 
    (SELECT TEM_ID FROM T_TEAM_TEM WHERE TEM_NAME LIKE 'Team B'));
    

/*Insertion des rencontres*/
INSERT INTO T_ENCOUNTER_ENC (ENC_SCORE_BLUE, ENC_SCORE_GREEN, ENC_STATE, TEM_ID_PARTICIPATE_BLUE, TEM_ID_PARTICIPATE_GREEN, CHP_ID)
VALUES
     (3, 1, 'Conclus', (SELECT TEM_ID FROM T_TEAM_TEM WHERE TEM_NAME LIKE 'Team A'),
     (SELECT TEM_ID FROM T_TEAM_TEM WHERE TEM_NAME LIKE 'Team B'), 
     (SELECT CHP_ID FROM T_CHAMPIONSHIP_CHP WHERE CHP_NAME LIKE 'Championship 2025')),
     
     (NULL, NULL, 'Programmée', (SELECT TEM_ID FROM T_TEAM_TEM WHERE TEM_NAME LIKE 'Team B'),
     (SELECT TEM_ID FROM T_TEAM_TEM WHERE TEM_NAME LIKE 'Team A'), 
     (SELECT CHP_ID FROM T_CHAMPIONSHIP_CHP WHERE CHP_NAME LIKE 'Championship 2026')),
     
     (NULL, NULL, 'Programmée', (SELECT TEM_ID FROM T_TEAM_TEM WHERE TEM_NAME LIKE 'Team B'),
     (SELECT TEM_ID FROM T_TEAM_TEM WHERE TEM_NAME LIKE 'Team C'), 
     (SELECT CHP_ID FROM T_CHAMPIONSHIP_CHP WHERE CHP_NAME LIKE 'Championship 2026'));
     
   
