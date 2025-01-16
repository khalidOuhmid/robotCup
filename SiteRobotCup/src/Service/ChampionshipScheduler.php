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
     * @param array $timeSlots Tableau de créneaux horaires
     * @param int $fieldId ID du terrain pour les rencontres
     * @return bool Succès de la génération
     */
    public function generateMatches(int $championshipId, array $timeSlots, int $fieldId): bool {
        try {
            error_log("Début de generateMatches - championshipId: $championshipId, fieldId: $fieldId");
            error_log("Nombre de créneaux: " . count($timeSlots));
            
            // 1. Récupérer toutes les équipes du championnat
            $teams = $this->getTeamsForChampionship($championshipId);
            error_log("Équipes trouvées: " . count($teams));
            
            if (count($teams) < 2) {
                throw new Exception("Il faut au moins 2 équipes pour générer des rencontres");
            }
            
            // 2. Générer toutes les combinaisons possibles
            $matches = $this->generateAllPossibleMatches($teams);
            error_log("Matchs possibles générés: " . count($matches));
            
            // 3. Assigner les créneaux horaires
            $scheduledMatches = $this->assignTimeSlots($matches, $timeSlots);
            error_log("Matchs programmés: " . count($scheduledMatches));
            
            // 4. Insérer les rencontres en base
            $this->insertMatches($scheduledMatches, $championshipId, $fieldId);
            error_log("Insertion en base terminée");
            
            return true;
        } catch (Exception $e) {
            error_log("Erreur dans generateMatches: " . $e->getMessage());
            throw $e; // Remonter l'exception pour la gestion d'erreur
        }
    }
    
    private function getTeamsForChampionship(int $championshipId): array {
        // Utiliser la requête SQL native avec les bons noms de tables/colonnes
        $query = "SELECT DISTINCT t.TEM_ID 
                 FROM T_TEAM_TEM t
                 JOIN T_COMPETITION_CMP c ON t.CMP_ID = c.CMP_ID
                 JOIN T_CHAMPIONSHIP_CHP ch ON ch.CMP_ID = c.CMP_ID
                 WHERE ch.CHP_ID = :championshipId
                 ORDER BY t.TEM_ID";  // Ajout d'un ORDER BY pour une génération consistante
                 
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['championshipId' => $championshipId]);
        
        $teams = $stmt->fetchAll(PDO::FETCH_COLUMN);
        error_log("Équipes trouvées: " . implode(', ', $teams));
        
        return $teams;
    }
    
    private function generateAllPossibleMatches(array $teams): array {
        $matches = [];
        $teamCount = count($teams);
        
        // Générer une seule fois chaque confrontation possible
        for ($i = 0; $i < $teamCount - 1; $i++) {
            for ($j = $i + 1; $j < $teamCount; $j++) {
                $matches[] = [
                    'blue_team' => $teams[$i],
                    'green_team' => $teams[$j],
                    'round' => 1
                ];
                // Ajouter le match retour avec les équipes inversées
                $matches[] = [
                    'blue_team' => $teams[$j],
                    'green_team' => $teams[$i],
                    'round' => 2
                ];
            }
        }
        
        error_log("Total des matchs générés: " . count($matches));
        return $matches;
    }

    private function assignTimeSlots(array $matches, array $timeSlots): array {
        $scheduledMatches = [];
        $matchIndex = 0;
        
        foreach ($timeSlots as $slot) {
            if ($matchIndex >= count($matches)) {
                break; // On arrête quand tous les matchs sont assignés
            }

            $scheduledMatches[] = [
                'blue_team' => $matches[$matchIndex]['blue_team'],
                'green_team' => $matches[$matchIndex]['green_team'],
                'start_time' => clone $slot['start'],
                'end_time' => clone $slot['end']
            ];
            $matchIndex++;
        }
        
        error_log("Matchs programmés: " . count($scheduledMatches) . " sur " . count($matches) . " possibles");
        return $scheduledMatches;
    }

    private function insertMatches(array $scheduledMatches, int $championshipId, int $fieldId): void {
        // Trouver le dernier créneau utilisé pour ce terrain
        $lastSlotQuery = "SELECT MAX(s.SLT_DATE_END) as last_end
                         FROM T_ENCOUNTER_ENC e
                         JOIN T_TIMESLOT_SLT s ON e.SLT_ID = s.SLT_ID
                         WHERE e.FLD_ID = :fieldId";
        $stmt = $this->pdo->prepare($lastSlotQuery);
        $stmt->execute(['fieldId' => $fieldId]);
        $lastEnd = $stmt->fetch(PDO::FETCH_COLUMN);

        // Déterminer le point de départ pour les nouveaux matchs
        $breakDuration = 10; // 10 minutes entre les matchs
        if ($lastEnd) {
            $startPoint = new \DateTime($lastEnd);
            $startPoint->modify("+$breakDuration minutes");
            error_log("Dernier créneau trouvé, début à: " . $startPoint->format('Y-m-d H:i:s'));
        } else {
            $startPoint = clone $scheduledMatches[0]['start_time'];
            error_log("Aucun créneau existant, début à: " . $startPoint->format('Y-m-d H:i:s'));
        }

        $currentTime = $startPoint;

        foreach ($scheduledMatches as $match) {
            try {
                $matchDuration = $match['end_time']->getTimestamp() - $match['start_time']->getTimestamp();
                $currentEndTime = clone $currentTime;
                $currentEndTime->modify('+' . floor($matchDuration/60) . ' minutes');

                error_log("Tentative d'insertion: début={$currentTime->format('Y-m-d H:i:s')}, fin={$currentEndTime->format('Y-m-d H:i:s')}");

                // Créer le TimeSlot
                $timeSlotQuery = "INSERT INTO T_TIMESLOT_SLT (SLT_DATE_BEGIN, SLT_DATE_END) 
                                VALUES (:startTime, :endTime)";
                $timeSlotStmt = $this->pdo->prepare($timeSlotQuery);
                $timeSlotStmt->execute([
                    'startTime' => $currentTime->format('Y-m-d H:i:s'),
                    'endTime' => $currentEndTime->format('Y-m-d H:i:s')
                ]);
                
                $timeSlotId = $this->pdo->lastInsertId();

                // Insérer la rencontre
                $encounterQuery = "INSERT INTO T_ENCOUNTER_ENC 
                                (CHP_ID, FLD_ID, TEM_ID_BLUE, TEM_ID_GREEN, ENC_STATE, SLT_ID,
                                 ENC_PENALTY_BLUE, ENC_PENALTY_GREEN)
                                VALUES 
                                (:championshipId, :fieldId, :blueTeam, :greenTeam, 'PROGRAMMEE', :timeSlotId,
                                 FALSE, FALSE)";
                $encounterStmt = $this->pdo->prepare($encounterQuery);
                $encounterStmt->execute([
                    'championshipId' => $championshipId,
                    'fieldId' => $fieldId,
                    'blueTeam' => $match['blue_team'],
                    'greenTeam' => $match['green_team'],
                    'timeSlotId' => $timeSlotId
                ]);

                // Préparer le prochain créneau
                $currentTime = clone $currentEndTime;
                $currentTime->modify("+$breakDuration minutes");

                error_log("Match inséré: bleu={$match['blue_team']}, vert={$match['green_team']}, début={$currentTime->format('Y-m-d H:i:s')}");
            } catch (Exception $e) {
                error_log("Erreur d'insertion: " . $e->getMessage());
                throw $e;
            }
        }
    }
}
