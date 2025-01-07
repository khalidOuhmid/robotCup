import pandas as pd
from sqlalchemy import create_engine
from sqlalchemy import text
from datetime import datetime

# Connection string
connection_string = "mysql+pymysql://etu_ibezie:CDegdb4f@info-titania/etu_ibezie"

# Create SQLAlchemy engine
engine = create_engine(connection_string)

# Connect to the database and test
with engine.connect() as connection:
    # Inserting data into T_CHAMPIONSHIP_CHP
    try:
        params = ({"name":'Test Championship', "date" : datetime(2025, 1, 7)})
        connection.execute(text(
            "INSERT INTO T_CHAMPIONSHIP_CHP (CHP_NAME, CHP_DATE) VALUES (:name, :date)"),
             params 
        )
        print("Insertion successful into T_CHAMPIONSHIP_CHP")
    except Exception as e:
        print(f"Error inserting into T_CHAMPIONSHIP_CHP: {e}")

    # Reading data
    try:
        df_championship = pd.read_sql("SELECT * FROM T_CHAMPIONSHIP_CHP", connection)
        print("Data from T_CHAMPIONSHIP_CHP:")
        print(df_championship)
    except Exception as e:
        print(f"Error reading from T_CHAMPIONSHIP_CHP: {e}")

    # Integrity check: ensure no duplicates in championship names
    if df_championship['CHP_NAME'].duplicated().any():
        print("Integrity error: duplicates found in championship names.")
    else:
        print("No duplicates in championship names.")

    
    # Inserting data into T_USER_USR
    try:
        params = ({"typeA":'ADMIN', "mailA" : 'test-admin-mail@mail.test', "passA" : 'testadminpass',
         "typeU":'USER', "mailU" : 'test-user-mail@mail.test', "passU" : 'testuserpass'})
        connection.execute(text(
            "INSERT INTO T_USER_USR (USR_TYPE, USR_MAIL, USR_PASS) VALUES (:typeA, :mailA, :passA), (:typeU, :mailU, :passU)"),
             params 
        )
        print("Insertion successful into T_USER_USR")
    except Exception as e:
        print(f"Error inserting into T_USER_USR: {e}")

    # Inserting data into T_USER_USR causing error (invalid type)
    try:
        params = ({"typeT":'TEST', "mailT" : 'test-false-mail@mail.test', "passT" : 'testfalsepass'})
        connection.execute(text(
            "INSERT INTO T_USER_USR (USR_TYPE, USR_MAIL, USR_PASS) VALUES (:typeT, :mailT, :passT)"),
             params 
        )
        print("Insertion successful into T_USER_USR")
    except Exception as e:
        print(f"Error inserting into T_USER_USR: {e}")

    # Reading data
    try:
        df_user = pd.read_sql("SELECT * FROM T_USER_USR", connection)
        print("Data from T_USER_USR:")
        print(df_user)
    except Exception as e:
        print(f"Error reading from T_USER_USR: {e}")

    # Integrity check: ensure no duplicates in user emails
    if df_user['USR_MAIL'].duplicated().any():
        print("Integrity error: duplicates found in user emails.")
    else:
        print("No duplicates in user emails.")


    # Inserting data into T_TEAM_TEM
    try:
        params = ({"nameA" : 'teamA-test', "scoreA" : 100, "nameB" : 'teamB-test', "scoreB" : 10})
        connection.execute(text(
            "INSERT INTO T_TEAM_TEM (USR_ID, TEM_NAME, TEM_SCORE) VALUES ((select USR_ID from T_USER_USR where USR_MAIL like 'test-user-mail@mail.test'), :nameA, :scoreA), ((select USR_ID from T_USER_USR where USR_MAIL like 'test-admin-mail@mail.test'), :nameB, :scoreB)"),
             params 
        )
        print("Insertion successful into T_TEAM_TEM")
    except Exception as e:
        print(f"Error inserting into T_TEAM_TEM: {e}")

    # Reading data
    try:
        df_user = pd.read_sql("SELECT * FROM T_TEAM_TEM", connection)
        print("Data from T_TEAM_TEM:")
        print(df_user)
    except Exception as e:
        print(f"Error reading from T_TEAM_TEM: {e}")

    # Integrity check: ensure no duplicates in team names
    if df_user['TEM_NAME'].duplicated().any():
        print("Integrity error: duplicates found in team names.")
    else:
        print("No duplicates in team names.")


    # Inserting data into T_MEMBER_MBR
    try:
        params = ({"nameA" : 'Jack', "surnameA" : 'Sparrow', "mailA" : 'theRealJack@pirate.us',
        "nameB" : 'Jeff', "surnameB": 'Buckley', "mailB": 'jeffBuckley@music.go'})
        connection.execute(text(
            """
            INSERT INTO T_MEMBER_MBR (TEM_ID, MBR_NAME, MBR_SURNAME, MBR_MAIL) 
            VALUES 
            (
                (SELECT TEM_ID FROM T_TEAM_TEM WHERE TEM_NAME LIKE 'teamA-test'),
                :nameA, :surnameA, :mailA
            ),
            (
                (SELECT TEM_ID FROM T_TEAM_TEM WHERE TEM_NAME LIKE 'teamB-test'),
                :nameB, :surnameB, :mailB
            )
        """),
             params 
        )
        print("Insertion successful into T_MEMBER_MBR")
    except Exception as e:
        print(f"Error inserting into T_MEMBER_MBR: {e}")

    # Reading data
    try:
        df_user = pd.read_sql("SELECT * FROM T_MEMBER_MBR", connection)
        print("Data from T_MEMBER_MBR:")
        print(df_user)
    except Exception as e:
        print(f"Error reading from T_MEMBER_MBR: {e}")

    # Integrity check: ensure no duplicates in member emails
    if df_user['MBR_MAIL'].duplicated().any():
        print("Integrity error: duplicates found in member emails.")
    else:
        print("No duplicates in member emails.")


    # Inserting data into T_ENCOUNTER_ENC
    try:
        params = ({"scoreB" : 5, "scoreG" : 10, "state" : 'Completed'})
        connection.execute(text(
            """
            INSERT INTO T_ENCOUNTER_ENC (TEM_ID_PARTICIPATE_BLUE, TEM_ID_PARTICIPATE_GREEN, 
            CHP_ID , ENC_SCORE_BLUE, ENC_SCORE_GREEN, ENC_STATE) 
            VALUES 
            (
                (SELECT TEM_ID FROM T_TEAM_TEM WHERE TEM_NAME LIKE 'teamA-test'),
                (SELECT TEM_ID FROM T_TEAM_TEM WHERE TEM_NAME LIKE 'teamB-test'),
                (SELECT CHP_ID FROM T_CHAMPIONSHIP_CHP WHERE CHP_NAME LIKE 'Test Championship'),
                :scoreB, :scoreG, :state
            )
        """),
             params 
        )
        print("Insertion successful into T_ENCOUNTER_ENC")
    except Exception as e:
        print(f"Error inserting into T_ENCOUNTER_ENC: {e}")

    # Inserting data into T_ENCOUNTER_ENC causes error (both teams are the same)
    try:
        params = ({"scoreB" : 5, "scoreG" : 10, "state" : 'Completed'})
        connection.execute(text(
            """
            INSERT INTO T_ENCOUNTER_ENC (TEM_ID_PARTICIPATE_BLUE, TEM_ID_PARTICIPATE_GREEN, 
            CHP_ID , ENC_SCORE_BLUE, ENC_SCORE_GREEN, ENC_STATE) 
            VALUES 
            (
                (SELECT TEM_ID FROM T_TEAM_TEM WHERE TEM_NAME LIKE 'teamA-test'),
                (SELECT TEM_ID FROM T_TEAM_TEM WHERE TEM_NAME LIKE 'teamA-test'),
                (SELECT CHP_ID FROM T_CHAMPIONSHIP_CHP WHERE CHP_NAME LIKE 'Test Championship'),
                :scoreB, :scoreG, :state
            )
        """),
             params 
        )
        print("Insertion successful into T_ENCOUNTER_ENC")
    except Exception as e:
        print(f"Error inserting into T_ENCOUNTER_ENC: {e}")

    # Reading data
    try:
        df_user = pd.read_sql("SELECT * FROM T_ENCOUNTER_ENC", connection)
        print("Data from T_ENCOUNTER_ENC:")
        print(df_user)
    except Exception as e:
        print(f"Error reading from T_ENCOUNTER_ENC: {e}")

    # Integrity check: ensure blue team is different from green team
    if df_user['TEM_ID_PARTICIPATE_BLUE'].equals(df_user['TEM_ID_PARTICIPATE_GREEN']):
        print("Integrity error: blue team is the same as green team.")
    else:
        print("The teams are different.")
