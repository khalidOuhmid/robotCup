<?php

namespace App\Service;

use PDO;
use Exception;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Championship;
use Psr\Log\LoggerInterface;
use App\Entity\Team;
use App\Entity\Encounter;
use App\Entity\TimeSlot;
use App\Entity\Field;

class ChampionshipScheduler {
    
    private EntityManagerInterface $entityManager;
    private PDO $pdo;
    private $logger;
    
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->pdo = $entityManager->getConnection()->getNativeConnection();
        $this->logger = $logger;
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

    public function generateChampionship(Championship $championship): void
    {
        $this->logger->info('Generating championship encounters', ['championship_id' => $championship->getId()]);

        switch ($championship->getType()) {
            case 'SUISSE':
                $this->generateSwissChampionship($championship, $championship->getCompetition()->getCmpRounds());
                break;
            case 'HOLLANDAIS':
                $this->generateDutchChampionship($championship);
                break;
            default:
                $this->generateNormalChampionship($championship);
        }
    }

    private function generateSwissChampionship(Championship $championship, int $rounds): void
    {
        $teams = $this->getTopTeams($championship->getCompetition());
        $teamsCount = count($teams);
        $fields = $this->getFields($championship->getCompetition());

        if ($teamsCount < 2) {
            $this->logger->error("Not enough teams to generate a championship", ['teams_count' => $teamsCount]);
            throw new \Exception("Pas assez d'équipes pour générer un championnat");
        }

        if (empty($fields)) {
            throw new \Exception("Aucun terrain n'est disponible pour cette compétition");
        }

        $maxRounds = min($rounds, floor(16 / $teamsCount));
        $timeSlots = $this->generateTimeSlots($championship, $maxRounds);
        $timeSlotIndex = 0;

        for ($round = 0; $round < $maxRounds; $round++) {
            $teamsCopy = $teams;
            shuffle($teamsCopy);

            for ($i = 0; $i < floor(count($teamsCopy) / 2); $i++) {
                if ($timeSlotIndex >= count($timeSlots)) break;

                $teamBlue = array_shift($teamsCopy);
                $teamGreen = array_shift($teamsCopy);

                if (!$teamBlue || !$teamGreen) break;

                $encounter = $this->generateEncounter(
                    $championship,
                    $teamBlue,
                    $teamGreen,
                    $timeSlots[$timeSlotIndex],
                    $fields[$i % count($fields)]
                );

                $this->entityManager->persist($encounter);
                $this->logger->info('Encounter created', [
                    'encounter' => [
                        'team_blue' => $encounter->getTeamBlue()->getName(),
                        'team_green' => $encounter->getTeamGreen()->getName(),
                        'time_slot' => $encounter->getTimeSlot()->getDateBegin()->format('Y-m-d H:i:s'),
                        'field' => $encounter->getField()->getName()
                    ]
                ]);
                $timeSlotIndex++;
            }
        }

        $this->entityManager->flush();
        $this->logger->info('Championship encounters generated successfully', ['championship_id' => $championship->getId()]);
    }

    private function generateDutchChampionship(Championship $championship): void
    {
        $teams = $this->getTopTeams($championship->getCompetition());
        $teamsCount = count($teams);

        if ($teamsCount < 2) {
            throw new \Exception("Pas assez d'équipes pour générer un championnat");
        }

        $timeSlots = $this->generateTimeSlots($championship, floor($teamsCount / 2));

        for ($i = 0; $i < floor($teamsCount / 2); $i++) {
            if ($i >= count($timeSlots)) break;

            $encounter = $this->generateEncounter(
                $championship,
                $teams[$i],
                $teams[$teamsCount - 1 - $i],
                $timeSlots[$i],
                $this->getFields($championship->getCompetition())[$i % count($this->getFields($championship->getCompetition()))]
            );

            $this->entityManager->persist($encounter);
        }

        $this->entityManager->flush();
    }

    private function generateNormalChampionship(Championship $championship): void
    {
        $teams = $this->getTopTeams($championship->getCompetition());
        $teamsCount = count($teams);

        if ($teamsCount < 4) {
            throw new \Exception("Il faut au moins 4 équipes pour générer un championnat éliminatoire");
        }

        $existingEncounters = $this->entityManager->getRepository(Encounter::class)
            ->findBy(['championship' => $championship]);
        if (!empty($existingEncounters)) {
            throw new \Exception("Ce championnat a déjà des rencontres programmées");
        }

        $numberOfRounds = ceil(log($teamsCount, 2));
        $totalTeamsNeeded = pow(2, $numberOfRounds);

        shuffle($teams);
        $teams = array_slice($teams, 0, min($teamsCount, $totalTeamsNeeded));

        $fields = $this->getFields($championship->getCompetition());
        if (empty($fields)) {
            throw new \Exception("Aucun terrain n'est disponible pour cette compétition");
        }

        $pairs = [];
        $usedTeams = [];
        for ($i = 0; $i < count($teams); $i += 2) {
            if ($i + 1 >= count($teams)) break;

            $teamBlue = $teams[$i];
            $teamGreen = $teams[$i + 1];

            $key = min($teamBlue->getId(), $teamGreen->getId()) . '_' . max($teamBlue->getId(), $teamGreen->getId());
            if (isset($usedTeams[$key])) {
                continue;
            }

            $pairs[] = [$teamBlue, $teamGreen];
            $usedTeams[$key] = true;
        }

        $timeSlots = $this->generateTimeSlots($championship, count($pairs));

        foreach ($pairs as $index => $pair) {
            if (!isset($timeSlots[$index])) break;

            $encounter = $this->generateEncounter(
                $championship,
                $pair[0],
                $pair[1],
                $timeSlots[$index],
                $fields[$index % count($fields)]
            );

            $this->entityManager->persist($encounter);
            $this->logger->info('Encounter created', [
                'teams' => [$pair[0]->getName(), $pair[1]->getName()],
                'time' => $timeSlots[$index]->getDateBegin()->format('Y-m-d H:i')
            ]);
        }

        $this->entityManager->flush();
    }

    private function generateTimeSlots(Championship $championship, int $numberOfSlots): array
    {
        $startDate = $championship->getCompetition()->getCmpDateBegin();
        $endDate = $championship->getCompetition()->getCmpDateEnd();
        $duration = 30;
        $break = 10;

        $timeSlots = [];
        $currentTime = clone $startDate;

        for ($i = 0; $i < $numberOfSlots; $i++) {
            $slotEnd = (clone $currentTime)->modify("+{$duration} minutes");
            if ($slotEnd > $endDate) {
                break;
            }

            $timeSlot = new TimeSlot();
            $timeSlot->setDateBegin($currentTime)
                ->setDateEnd($slotEnd);

            $existingTimeSlot = $this->entityManager->getRepository(TimeSlot::class)
                ->findOneBy([
                    'dateBegin' => $currentTime,
                    'dateEnd' => $slotEnd
                ]);

            if ($existingTimeSlot) {
                $timeSlots[] = $existingTimeSlot;
            } else {
                $this->entityManager->persist($timeSlot);
                $timeSlots[] = $timeSlot;
            }

            $currentTime = (clone $slotEnd)->modify("+{$break} minutes");
        }

        $this->entityManager->flush();
        return $timeSlots;
    }

    private function getFields($competition): array
    {
        return $this->entityManager->getRepository(Field::class)
            ->findBy(['competition' => $competition]);
    }

    private function getTopTeams($competition, $limit = 32): array
    {
        return $this->entityManager->getRepository(Team::class)
            ->findBy(['competition' => $competition], ['id' => 'ASC'], $limit);
    }

    private function generateEncounter(Championship $championship, Team $teamBlue, Team $teamGreen, TimeSlot $timeSlot, Field $field): Encounter
    {
        $encounter = new Encounter();
        $encounter->setChampionship($championship)
            ->setTimeSlot($timeSlot)
            ->setField($field)
            ->setTeamBlue($teamBlue)
            ->setTeamGreen($teamGreen)
            ->setState('PROGRAMMEE')
            ->setPenaltyBlue(false)
            ->setPenaltyGreen(false)
            ->setFixedScore(false);

        return $encounter;
    }
}
