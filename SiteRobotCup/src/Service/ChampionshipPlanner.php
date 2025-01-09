<?php
namespace App\Service;

use PDO;
use Exception;

class ChampionshipPlanner
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getTeams(): array
    {
        $query = "SELECT TEM_ID, TEM_NAME FROM T_Team_Tem";
        $statement = $this->db->query($query);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function generateSchedule(array $teams, int $championshipId): array
    {
        $schedule = [];
        $totalTeams = count($teams);

        if ($totalTeams < 2) {
            throw new Exception("Il faut au moins deux équipes pour créer un championnat.");
        }

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
        $query = "INSERT INTO T_ENCOUNTER_ENC (
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
                    0, 
                    0, 
                    'pending'
                  )";

        $statement = $this->db->prepare($query);

        foreach ($schedule as $match) {
            $statement->execute([
                'blue_team_id' => $match['blue_team_id'],
                'green_team_id' => $match['green_team_id'],
                'championship_id' => $match['championship_id'],
            ]);
        }
    }
}
