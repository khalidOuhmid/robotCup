<?php
namespace App\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class ChampionshipPlanner
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function getTeams(): array
    {
        $query = "SELECT TEM_ID, TEM_NAME FROM T_TEAM_TEM";

        // Utilisation de la méthode executeQuery pour récupérer les équipes
        return $this->db->executeQuery($query)->fetchAllAssociative();
    }

    public function generateSchedule(array $teams, int $championshipId): array
    {
        $schedule = [];
        $totalTeams = count($teams);

        for ($i = 0; $i < $totalTeams - 1; $i++) {
            for ($j = $i + 1; $j < $totalTeams; $j++) {
                $schedule[] = [
                    'blue_team_id' => $teams[$i]['TEM_ID'],
                    'green_team_id' => $teams[$j]['TEM_ID'],
                    'championship_id' => $championshipId,
                ];
            }
        }

        shuffle($schedule);
        return $schedule;
    }

    public function saveSchedule(array $schedule): void
    {
        $queryCheck = "SELECT COUNT(*) FROM T_ENCOUNTER_ENC 
                    WHERE 
                        (TEM_ID_PARTICIPATE_BLUE = :blue_team_id AND TEM_ID_PARTICIPATE_GREEN = :green_team_id)
                        OR 
                        (TEM_ID_PARTICIPATE_BLUE = :green_team_id AND TEM_ID_PARTICIPATE_GREEN = :blue_team_id)";
                        
        $queryInsert = "INSERT INTO T_ENCOUNTER_ENC (
                            TEM_ID_PARTICIPATE_BLUE, 
                            TEM_ID_PARTICIPATE_GREEN, 
                            CHP_ID, 
                            ENC_SCORE_BLUE, 
                            ENC_SCORE_GREEN, 
                            ENC_STATE
                        ) VALUES (
                            :blue_team_id, 
                            :green_team_id, 
                            :championship_id, 
                            NULL, 
                            NULL, 
                            'pending'
                        )";

        $statementCheck = $this->db->prepare($queryCheck);
        $statementInsert = $this->db->prepare($queryInsert);

        foreach ($schedule as $match) {
            // Vérification des doublons
            $result = $statementCheck->executeQuery([
                'blue_team_id' => $match['blue_team_id'],
                'green_team_id' => $match['green_team_id'],
            ]);

            $existingCount = $result->fetchOne();

            if ($existingCount == 0) {
                // Insérer uniquement si aucune rencontre similaire n'existe
                $statementInsert->executeStatement([
                    'blue_team_id' => $match['blue_team_id'],
                    'green_team_id' => $match['green_team_id'],
                    'championship_id' => $match['championship_id'],
                ]);
            }
        }
    }


    public function getMatches(): array
    {
        $query = "SELECT 
                    ENC_ID, 
                    TEM_ID_PARTICIPATE_BLUE AS blue_team_id, 
                    TEM_ID_PARTICIPATE_GREEN AS green_team_id, 
                    ENC_SCORE_BLUE AS score_blue, 
                    ENC_SCORE_GREEN AS score_green,
                    ENC_STATE AS state
                FROM T_ENCOUNTER_ENC
                ORDER BY ENC_ID ASC";

        $results = $this->db->executeQuery($query)->fetchAllAssociative();

        foreach ($results as &$match) {
            $match['blue_team_name'] = $this->getTeamName($match['blue_team_id']);
            $match['green_team_name'] = $this->getTeamName($match['green_team_id']);
            $match['enc_id'] = $match['ENC_ID'];
        }

        return $results;
    }


    



    private function getTeamName(int $teamId): string
    {
        $query = "SELECT TEM_NAME FROM T_TEAM_TEM WHERE TEM_ID = :team_id";
        return $this->db->fetchOne($query, ['team_id' => $teamId]);
    }

}
