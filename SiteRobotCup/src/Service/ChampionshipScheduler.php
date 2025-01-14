<?php

namespace App\Service;

use PDO;
use Exception;
use Doctrine\ORM\EntityManagerInterface;

class ChampionshipScheduler {
    
    private EntityManagerInterface $entityManager;
    private PDO $pdo;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->pdo = $entityManager->getConnection()->getNativeConnection();
    }
    
    /**
     * Génère toutes les rencontres possibles pour un championnat
     * @param int $championshipId ID du championnat
     * @param array $timeSlots Tableau de créneaux horaires [['start' => DateTime, 'end' => DateTime, 'count' => int]]
     * @param int $fieldId ID du terrain pour les rencontres
     * @return bool Succès de la génération
     */
    public function generateMatches(int $championshipId, array $timeSlots, int $fieldId): bool {
        try {
            // 1. Récupérer toutes les équipes du championnat
            $teams = $this->getTeamsForChampionship($championshipId);
            if (count($teams) < 2) {
                throw new Exception("Il faut au moins 2 équipes pour générer des rencontres");
            }
            
            // 2. Générer toutes les combinaisons possibles
            $matches = $this->generateAllPossibleMatches($teams);
            
            // 3. Assigner les créneaux horaires
            $scheduledMatches = $this->assignTimeSlots($matches, $timeSlots);
            
            // 4. Insérer les rencontres en base
            $this->insertMatches($scheduledMatches, $championshipId, $fieldId);
            
            return true;
        } catch (Exception $e) {
            // Log l'erreur ou la gérer selon vos besoins
            return false;
        }
    }
    
    private function getTeamsForChampionship(int $championshipId): array {
        $query = "SELECT DISTINCT t.TEM_ID 
                 FROM T_TEAM_TEM t
                 JOIN T_COMPETITION_CMP c ON t.CMP_ID = c.CMP_ID
                 JOIN T_CHAMPIONSHIP_CHP chp ON chp.CMP_ID = c.CMP_ID
                 WHERE chp.CHP_ID = :championshipId";
                 
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['championshipId' => $championshipId]);
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    private function generateAllPossibleMatches(array $teams): array {
        $matches = [];
        $teamCount = count($teams);
        
        for ($i = 0; $i < $teamCount - 1; $i++) {
            for ($j = $i + 1; $j < $teamCount; $j++) {
                $matches[] = [
                    'blue_team' => $teams[$i],
                    'green_team' => $teams[$j]
                ];
            }
        }
        
        return $matches;
    }
    
    private function assignTimeSlots(array $matches, array $timeSlots): array {
        $scheduledMatches = [];
        $matchIndex = 0;
        
        foreach ($timeSlots as $slot) {
            for ($i = 0; $i < $slot['count'] && $matchIndex < count($matches); $i++) {
                $scheduledMatches[] = [
                    'blue_team' => $matches[$matchIndex]['blue_team'],
                    'green_team' => $matches[$matchIndex]['green_team'],
                    'start_time' => clone $slot['start'],
                    'end_time' => clone $slot['end']
                ];
                $matchIndex++;
            }
        }
        
        if ($matchIndex < count($matches)) {
            throw new Exception("Pas assez de créneaux horaires pour toutes les rencontres");
        }
        
        return $scheduledMatches;
    }
    
    private function insertMatches(array $scheduledMatches, int $championshipId, int $fieldId): void {
        $query = "INSERT INTO T_ENCOUNTER_ENC 
                 (CHP_ID, FLD_ID, TEM_ID_BLUE, TEM_ID_GREEN, ENC_STATE, 
                  ENC_DATE_BEGIN, ENC_DATE_END, ENC_SCORE_BLUE, ENC_SCORE_GREEN)
                 VALUES 
                 (:championshipId, :fieldId, :blueTeam, :greenTeam, 'PROGRAMMEE',
                  :startTime, :endTime, NULL, NULL)";
                  
        $stmt = $this->pdo->prepare($query);
        
        foreach ($scheduledMatches as $match) {
            $stmt->execute([
                'championshipId' => $championshipId,
                'fieldId' => $fieldId,
                'blueTeam' => $match['blue_team'],
                'greenTeam' => $match['green_team'],
                'startTime' => $match['start_time']->format('Y-m-d H:i:s'),
                'endTime' => $match['end_time']->format('Y-m-d H:i:s')
            ]);
        }
    }
}
