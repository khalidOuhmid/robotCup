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
    with connection.begin() as transaction:
        # Inserting data into T_COMPETITION_CMP
        try:
            connection.execute(
                text("""INSERT INTO T_COMPETITION_CMP (CMP_ADDRESS, CMP_DATE_BEGIN, CMP_DATE_END, CMP_NAME, CMP_DESC) 
                    VALUES ('TEST 123 Main St', '2000-01-01', '2000-01-20', 'TESTWinter Championship', 'TEST Annual winter event'),
                    ('TEST 456 Elm St', '2000-02-01', '2000-02-10', 'TESTSpring Championship', 'TEST Annual spring event');"""),
            )
            print("Insertion successful into T_COMPETITION_CMP")
        except Exception as e:
            print(f"Error inserting into T_COMPETITION_CMP: {e}")

        # Inserting data into T_COMPETITION_CMP causing error (Overlap time)
        try:
            connection.execute(
                text("""INSERT INTO T_COMPETITION_CMP (CMP_ADDRESS, CMP_DATE_BEGIN, CMP_DATE_END, CMP_NAME, CMP_DESC)
                    VALUES ('TEST789 Pine St', '2025-01-05', '2025-01-08', 'TESTOverlap Test', 'TESTThis should fail');"""),
            )
            print("Insertion successful into T_COMPETITION_CMP")
        except Exception as e:
            print(f"TEST : Error inserting into T_COMPETITION_CMP: Dates overlap")

        # Reading data from T_COMPETITION_CMP
        try:
            df_competition = pd.read_sql("SELECT * FROM T_COMPETITION_CMP", connection)
            print("Data from T_COMPETITION_CMP:")
            print(df_competition)
        except Exception as e:
            print(f"Error reading from T_COMPETITION_CMP: {e}")

        # Integrity check: Ensure no duplicates in competition names
        if df_competition['CMP_NAME'].duplicated().any():
            print("Integrity error: duplicates found in competition names.")
        else:
            print("No duplicates in competition names.")
        print(f"Total competitions: {len(df_competition)}")


        # Inserting data into T_CHAMPIONSHIP_CHP
        try:
            connection.execute(text("""INSERT INTO T_CHAMPIONSHIP_CHP (CMP_ID)
                                VALUES ((select CMP_ID from T_COMPETITION_CMP where CMP_NAME like 'TESTSpring Championship'));""")
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
        
        # Inserting data into T_TOURNAMENT_TNM 
        try:
            connection.execute(text("""INSERT INTO T_TOURNAMENT_TNM (CMP_ID) VALUES ((select CMP_ID from T_COMPETITION_CMP where CMP_NAME like 'TESTSpring Championship'));""")
            )
            print("Insertion successful into T_TOURNAMENT_TNM")
        except Exception as e:
            print(f"Error inserting into T_TOURNAMENT_TNM: {e}")

        # Reading data
        try:
            df_championship = pd.read_sql("SELECT * FROM T_TOURNAMENT_TNM", connection)
            print("Data from T_TOURNAMENT_TNM:")
            print(df_championship)
        except Exception as e:
            print(f"Error reading from T_TOURNAMENT_TNM: {e}")


        # Inserting data into T_USER_USR
        try:
            connection.execute(text(
                """ INSERT INTO T_USER_USR (USR_TYPE, USR_MAIL, USR_PASS, USR_CREATION_DATE)
                    VALUES ('ADMIN', 'TESTadmin@example.com', 'TESTsecurepassword', '2025-01-01'),
                    ('USER', 'TESTuser1@example.com', 'TESTpassword123', '2025-01-02'),
                    ('USER', 'TESTuser2@example.com', 'TESTpassword123', '2025-01-02');
                """),
            )
            print("Insertion successful into T_USER_USR")
        except Exception as e:
            print(f"Error inserting into T_USER_USR: {e}")

        # Inserting data into T_USER_USR causing error (invalid type)
        try:
            connection.execute(text(
                """ INSERT INTO T_USER_USR (USR_TYPE, USR_MAIL, USR_PASS, USR_CREATION_DATE)
                    VALUES ('TEST', 'test-false-mail@mail.test', 'securepassword', '2025-01-01')""")
            )
            print("Insertion successful into T_USER_USR")
        except Exception as e:
            print(f"TEST : Error inserting into T_USER_USR: Invalid user type")

        # Inserting data into T_USER_USR causing error (duplicate email)
        try:
            connection.execute(text(
                """ INSERT INTO T_USER_USR (USR_TYPE, USR_MAIL, USR_PASS, USR_CREATION_DATE)
                    VALUES ('USER', 'TESTuser2@example.com', 'TESTtest123', '2025-01-03')""")
            )
            print("Insertion successful into T_USER_USR")
        except Exception as e:
            print(f"TEST : Error inserting into T_USER_USR: Duplicate email")

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
            connection.execute(text(
                """ INSERT INTO T_TEAM_TEM (USR_ID, TEM_NAME, TEM_CREATION_DATE, CMP_ID)
                    VALUES ((select USR_ID from T_USER_USR where USR_MAIL like 'TESTuser1@example.com'), 'TESTBlue Warriors', '2025-01-05', (select CMP_ID from T_COMPETITION_CMP where CMP_NAME like 'TESTSpring Championship')),
                    ((select USR_ID from T_USER_USR where USR_MAIL like 'TESTuser2@example.com'), 'TESTGreen Giants', '2025-01-06', (select CMP_ID from T_COMPETITION_CMP where CMP_NAME like 'TESTWinter Championship'));""")
            )
            print("Insertion successful into T_TEAM_TEM")
        except Exception as e:
            print(f"Error inserting into T_TEAM_TEM: {e}")

        # Inserting data into T_TEAM_TEM causing error (Team by same user for same competition)
        try:
            connection.execute(text(
                """ INSERT INTO T_TEAM_TEM (USR_ID, TEM_NAME, TEM_CREATION_DATE, CMP_ID)
                    VALUES ((select USR_ID from T_USER_USR  where USR_MAIL like 'TESTuser1@example.com'), 'TESTGrey Things', '2025-01-07', (select CMP_ID from T_COMPETITION_CMP where CMP_NAME like 'TESTSpring Championship'));
                    """)
            )
            print("Insertion successful into T_TEAM_TEM")
        except Exception as e:
            print(f"TEST : Error inserting into T_TEAM_TEM: Team by same user for same competition")

        # Inserting data into T_TEAM_TEM causing error (Team with same name into same competition)
        try:
            connection.execute(text(
                """ INSERT INTO T_TEAM_TEM (USR_ID, TEM_NAME, TEM_CREATION_DATE, CMP_ID)
                    VALUES ((select USR_ID from T_USER_USR  where USR_MAIL like 'TESTuser2@example.com'), 'TESTBlue Warriors', '2025-01-07', (select CMP_ID from T_COMPETITION_CMP where CMP_NAME like 'TESTSpring Championship'));
                    """)
            )
            print("Insertion successful into T_TEAM_TEM")
        except Exception as e:
            print(f"TEST : Error inserting into T_TEAM_TEM: Team with same name into same competition")

        # Reading data
        try:
            df_user = pd.read_sql("SELECT * FROM T_TEAM_TEM", connection)
            print("Data from T_TEAM_TEM:")
            print(df_user)
        except Exception as e:
            print(f"Error reading from T_TEAM_TEM: {e}")


        # Inserting data into T_MEMBER_MBR
        try:
            connection.execute(text(
                """ INSERT INTO T_MEMBER_MBR (TEM_ID, MBR_NAME, MBR_SURNAME, MBR_MAIL)
                    VALUES ((select TEM_ID from T_TEAM_TEM where TEM_NAME like 'TESTBlue Warriors'), 'John', 'Doe', 'TESTjohn.doe@example.com'),  
                    ((select TEM_ID from T_TEAM_TEM where TEM_NAME like 'TESTGreen Giants'), 'Jane', 'Smith', 'TESTjane.smith@example.com');
                    """)
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


        # Inserting data into T_FIELD_FLD
        try:
            connection.execute(
                text("INSERT INTO T_FIELD_FLD (CMP_ID, FLD_NAME) VALUES ((select CMP_ID from T_COMPETITION_CMP where CMP_NAME like 'TESTSpring Championship'), 'TESTField A'), ((select CMP_ID from T_COMPETITION_CMP where CMP_NAME like 'TESTSpring Championship'), 'TESTField B');")
            )
            print("Insertion successful into T_FIELD_FLD")
        except Exception as e:
            print(f"Error inserting into T_FIELD_FLD: {e}")

        # Reading data from T_FIELD_FLD
        try:
            df_field = pd.read_sql("SELECT * FROM T_FIELD_FLD", connection)
            print("Data from T_FIELD_FLD:")
            print(df_field)
        except Exception as e:
            print(f"Error reading from T_FIELD_FLD: {e}")

        # Integrity check: Ensure no duplicates in field names
        if df_field['FLD_NAME'].duplicated().any():
            print("Integrity error: duplicates found in field names.")
        else:
            print("No duplicates in field names.")
        print(f"Total fields: {len(df_field)}")

    # Inserting data into T_TIMESLOT_SLT
        try:
            connection.execute(text(
                """
                INSERT INTO T_TIMESLOT_SLT (SLT_DATE_BEGIN, SLT_DATE_END)
                VALUES ('2000-01-07 10:00:00', '2000-01-07 12:00:00'),
                ('2000-01-07 11:00:00', '2000-01-07 13:00:00'),
                ('2000-01-07 09:00:00', '2000-01-07 11:00:00');
                """)
            )
            print("Insertion successful into T_TIMESLOT_SLT")
        except Exception as e:
            print(f"Error inserting into T_TIMESLOT_SLT: {e}")


        try:
            df_user = pd.read_sql("SELECT * FROM T_TIMESLOT_SLT", connection)
            print("Data from T_TIMESLOT_SLT:")
            print(df_user)
        except Exception as e:
                print(f"Error reading from T_TIMESLOT_SLT: {e}")

    # Integrity check: ensure beginning date is before end date    
        if (df_user['SLT_DATE_BEGIN'] > df_user['SLT_DATE_BEGIN']).all():
            print("Integrity error: beginning date is after end date.")
        else:
            print("Beginning date is always before end date.")

    # Inserting data into T_ENCOUNTER_ENC
        try:
            connection.execute(text(
                """
                INSERT INTO T_ENCOUNTER_ENC (TNM_ID, CHP_ID, FLD_ID, TEM_ID_BLUE, TEM_ID_GREEN, ENC_STATE, SLT_ID, ENC_SCORE_BLUE, ENC_SCORE_GREEN, ENC_PENALTY_BLUE, ENC_PENALTY_GREEN)
                VALUES (NULL, 
                (select CHP_ID from T_CHAMPIONSHIP_CHP where CMP_ID = (select CMP_ID from T_COMPETITION_CMP where CMP_NAME like 'TESTSpring Championship')),
                (select FLD_ID from T_FIELD_FLD where FLD_NAME like 'TESTField A'),
                (select TEM_ID from T_TEAM_TEM where TEM_NAME like 'TESTBlue Warriors'),
                (select TEM_ID from T_TEAM_TEM where TEM_NAME like 'TESTGreen Giants'),
                'CONCLUE', (select SLT_ID from T_TIMESLOT_SLT where SLT_DATE_BEGIN = '2000-01-07 10:00:00')
                , 3, 1, FALSE, FALSE);
                """)
            )
            print("Insertion successful into T_ENCOUNTER_ENC")
        except Exception as e:
            print(f"Error inserting into T_ENCOUNTER_ENC: {e}")


        # Inserting data into T_ENCOUNTER_ENC causes error (same teams and overlapping dates)
        try:
            connection.execute(text(
                """
                INSERT INTO T_ENCOUNTER_ENC (TNM_ID, CHP_ID, FLD_ID, TEM_ID_BLUE, TEM_ID_GREEN, ENC_STATE, SLT_ID, ENC_SCORE_BLUE, ENC_SCORE_GREEN, ENC_PENALTY_BLUE, ENC_PENALTY_GREEN)
                VALUES (NULL, 
                (select CHP_ID from T_CHAMPIONSHIP_CHP where CMP_ID = (select CMP_ID from T_COMPETITION_CMP where CMP_NAME like 'TESTSpring Championship')),
                (select FLD_ID from T_FIELD_FLD where FLD_NAME like 'TESTField B'),
                (select TEM_ID from T_TEAM_TEM where TEM_NAME like 'TESTBlue Warriors'),
                (select TEM_ID from T_TEAM_TEM where TEM_NAME like 'TESTGreen Giants'),
                'CONCLUE', (select SLT_ID from T_TIMESLOT_SLT where SLT_DATE_BEGIN = '2000-01-07 11:00:00')
                , 2, 1, FALSE, FALSE);
            """)
            )
            print("FAIL : Insertion successful into T_ENCOUNTER_ENC Duplicate teams with overlapping dates")
        except Exception as e:
            print(f"TEST : Error inserting into T_ENCOUNTER_ENC: Duplicate teams with overlapping dates")

        # Inserting data into T_ENCOUNTER_ENC causes error (same field and overlapping dates)
        try:
            connection.execute(text(
                """
                INSERT INTO T_ENCOUNTER_ENC (TNM_ID, CHP_ID, FLD_ID, TEM_ID_BLUE, TEM_ID_GREEN, ENC_STATE, SLT_ID, ENC_SCORE_BLUE, ENC_SCORE_GREEN, ENC_PENALTY_BLUE, ENC_PENALTY_GREEN)
                VALUES (NULL, 
                (select CHP_ID from T_CHAMPIONSHIP_CHP where CMP_ID = (select CMP_ID from T_COMPETITION_CMP where CMP_NAME like 'TESTSpring Championship')),
                (select FLD_ID from T_FIELD_FLD where FLD_NAME like 'TESTField A'),
                (select TEM_ID from T_TEAM_TEM where TEM_NAME like 'TESTBlue Warriors'),
                (select TEM_ID from T_TEAM_TEM where TEM_NAME like 'TESTGreen Giants'), 
                'PROGRAMMEE', (select SLT_ID from T_TIMESLOT_SLT where SLT_DATE_BEGIN = '2000-01-07 09:00:00'), NULL, NULL, FALSE, FALSE);
                """)
            )
            print("FAIL : Insertion successful into T_ENCOUNTER_ENC Duplicate field with overlapping dates")
        except Exception as e:
            print(f"TEST : Error inserting into T_ENCOUNTER_ENC: Duplicate field with overlapping dates")

        # Reading data
        try:
            df_user = pd.read_sql("SELECT * FROM T_ENCOUNTER_ENC", connection)
            print("Data from T_ENCOUNTER_ENC:")
            print(df_user)
        except Exception as e:
            print(f"Error reading from T_ENCOUNTER_ENC: {e}")


        # Integrity check: ensure blue team is different from green team
        if df_user['TEM_ID_BLUE'].equals(df_user['TEM_ID_GREEN']):
            print("Integrity error: blue team is the same as green team.")
        else:
            print("The teams are different.")

        transaction.rollback()
engine.dispose()
