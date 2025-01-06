/*
 ----------------------------------------------------------------------------
             Génération d'une base de données pour
                        SQL Server 2005
                       (6/1/2025 15:23:22)
 ----------------------------------------------------------------------------
     Nom de la base : ROBOT_CHAMPIONSHIP
     Projet : Accueil WinDesign V17
     Auteur : CRIIUT
     Date de dernière modification : 6/1/2025 15:23:08
 ----------------------------------------------------------------------------
*/
use master 

drop database ROBOT_CHAMPIONSHIP
go

/* -----------------------------------------------------------------------------
      OUVERTURE DE LA BASE ROBOT_CHAMPIONSHIP
----------------------------------------------------------------------------- */

create database ROBOT_CHAMPIONSHIP
go

use ROBOT_CHAMPIONSHIP
go

CREATE USER [IUTBX\kouhmid]
CREATE USER [IUTBX\cbenony]
CREATE USER [IUTBX\nabe]
CREATE USER [IUTBX\nlaubal]
CREATE USER [IUTBX\fdebailleul]
go

grant view definition, select, update, insert, delete
to [IUTBX\kouhmid], [IUTBX\cbenony], [IUTBX\nabe], [IUTBX\nlaubal], [IUTBX\fdebailleul]
go


/* -----------------------------------------------------------------------------
      TABLE : T_CHAMPIONSHIP_CHP
----------------------------------------------------------------------------- */

create table T_CHAMPIONSHIP_CHP
  (
     CHP_ID int  not null  ,
     CHP_NAME char(255)  not null  ,
     CHP_DATE datetime  not null  
     ,
     constraint PK_T_CHAMPIONSHIP_CHP primary key (CHP_ID)
  ) 
go



/* -----------------------------------------------------------------------------
      TABLE : T_TEAM_TEM
----------------------------------------------------------------------------- */

create table T_TEAM_TEM
  (
     TEM_ID int  not null  ,
     TEM_NAME char(255)  not null  ,
     TEM_SCORE int  null  
     ,
     constraint PK_T_TEAM_TEM primary key (TEM_ID)
  ) 
go



/* -----------------------------------------------------------------------------
      TABLE : T_USER_USR
----------------------------------------------------------------------------- */

create table T_USER_USR
  (
     USR_ID int  not null  ,
     TEM_ID int  null  ,
     USR_TYPE char(255)  not null  ,
     USR_MAIL char(255)  not null  ,
     USR_PASS varchar(128)  not null  ,
     USR_TEAM char(32)  null  
     ,
     constraint PK_T_USER_USR primary key (USR_ID)
  ) 
go



/*      INDEX DE T_USER_USR      */



/* -----------------------------------------------------------------------------
      TABLE : T_ENCOUNTER_ENC
----------------------------------------------------------------------------- */

create table T_ENCOUNTER_ENC
  (
     ENC_ID int  not null  ,
     CHP_ID int  not null  ,
     ENC_SCORE_A int  null  ,
     ENC_SCORE_B int  null  
     ,
     constraint PK_T_ENCOUNTER_ENC primary key (ENC_ID)
  ) 
go



/*      INDEX DE T_ENCOUNTER_ENC      */



/* -----------------------------------------------------------------------------
      TABLE : PARTICIPATE
----------------------------------------------------------------------------- */

create table PARTICIPATE
  (
     TEM_ID int  not null  ,
     ENC_ID int  not null  
     ,
     constraint PK_PARTICIPATE primary key (TEM_ID, ENC_ID)
  ) 
go



/*      INDEX DE PARTICIPATE      */



/* -----------------------------------------------------------------------------
      REFERENCES SUR LES TABLES
----------------------------------------------------------------------------- */



alter table T_USER_USR 
     add constraint FK_T_USER_USR_T_TEAM_TEM foreign key (TEM_ID) 
               references T_TEAM_TEM (TEM_ID)
go




alter table T_ENCOUNTER_ENC 
     add constraint FK_T_ENCOUNTER_ENC_T_CHAMPIONSHIP_CHP foreign key (CHP_ID) 
               references T_CHAMPIONSHIP_CHP (CHP_ID)
go




alter table PARTICIPATE 
     add constraint FK_PARTICIPATE_T_TEAM_TEM foreign key (TEM_ID) 
               references T_TEAM_TEM (TEM_ID)
go




alter table PARTICIPATE 
     add constraint FK_PARTICIPATE_T_ENCOUNTER_ENC foreign key (ENC_ID) 
               references T_ENCOUNTER_ENC (ENC_ID)
go


alter table T_USER_USR 
	 add constraint UNIQUE_T_USER_USR UNIQUE (USR_MAIL)
go


alter table T_TEAM_TEM 
	 add constraint UNIQUE_T_TEAM_TEM UNIQUE (TEM_NAME)
go


alter table T_CHAMPIONSHIP_CHP
	 add constraint UNIQUE_T_CHAMPIONSHIP_CHP UNIQUE (CHP_NAME)
go

/*
 -----------------------------------------------------------------------------
               FIN DE GENERATION
 -----------------------------------------------------------------------------
*/