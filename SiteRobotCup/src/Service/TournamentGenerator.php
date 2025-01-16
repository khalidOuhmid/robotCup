<?php
// src/Service/TournamentGenerator.php
namespace App\Service;

use App\Entity\Tournament;
use App\Entity\Team;
use App\Entity\Encounter;
use App\Entity\TimeSlot;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

class TournamentGenerator
{
    private $entityManager;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function generateTournament(Tournament $tournament): void
    {
        $this->logger->info('Generating tournament', [
            'tournament_id' => $tournament->getId(),
            'type' => $tournament->getType()
        ]);

        switch ($tournament->getType()) {
            case 'SUISSE':
                $this->generateSwissTournament($tournament, $tournament->getCompetition()->getCmpRounds());
                break;
            case 'HOLLANDAIS':
                $this->generateDutchTournament($tournament);
                break;
            default:
                $this->generateNormalTournament($tournament);
        }

        $this->logger->info('Tournament generation completed', [
            'tournament_id' => $tournament->getId()
        ]);
    }

    private function generateSwissTournament(Tournament $tournament, int $rounds): void
    {
        $this->logger->info('Generating Swiss tournament', [
            'tournament_id' => $tournament->getId(),
            'rounds' => $rounds
        ]);

        $teams = $this->getTopTeams($tournament->getCompetition());
        $teamsCount = count($teams);

        if ($teamsCount < 2) {
            throw new \Exception("Pas assez d'équipes pour générer un tournoi");
        }

        $maxRounds = min($rounds, floor(16 / $teamsCount));
        $timeSlots = $this->generateTimeSlots($tournament, $maxRounds);
        $timeSlotIndex = 0;

        for ($round = 0; $round < $maxRounds; $round++) {
            usort($teams, function ($a, $b) {
                return ($b->getPoints() ?? 0) - ($a->getPoints() ?? 0);
            });

            for ($i = 0; $i < floor($teamsCount / 2); $i++) {
                if ($timeSlotIndex >= count($timeSlots)) break;

                $encounter = new Encounter();
                $encounter->setTournament($tournament)
                    ->setTimeSlot($timeSlots[$timeSlotIndex])
                    ->setTeamBlue($teams[$i * 2])
                    ->setTeamGreen($teams[$i * 2 + 1])
                    ->setState('PROGRAMMEE');

                $this->entityManager->persist($encounter);
                $timeSlotIndex++;
            }
        }

        $this->entityManager->flush();

        $this->logger->info('Swiss tournament generation completed', [
            'tournament_id' => $tournament->getId()
        ]);
    }

    private function generateDutchTournament(Tournament $tournament): void
    {
        $this->logger->info('Generating Dutch tournament', [
            'tournament_id' => $tournament->getId()
        ]);

        $teams = $this->getTopTeams($tournament->getCompetition());
        $teamsCount = count($teams);

        if ($teamsCount < 2) {
            throw new \Exception("Pas assez d'équipes pour générer un tournoi");
        }

        $timeSlots = $this->generateTimeSlots($tournament, floor($teamsCount / 2));

        for ($i = 0; $i < floor($teamsCount / 2); $i++) {
            if ($i >= count($timeSlots)) break;

            $encounter = new Encounter();
            $encounter->setTournament($tournament)
                ->setTimeSlot($timeSlots[$i])
                ->setTeamBlue($teams[$i])
                ->setTeamGreen($teams[$teamsCount - 1 - $i])
                ->setState('PROGRAMMEE');

            $this->entityManager->persist($encounter);
        }

        $this->entityManager->flush();

        $this->logger->info('Dutch tournament generation completed', [
            'tournament_id' => $tournament->getId()
        ]);
    }

    public function generateNormalTournament(Tournament $tournament): void
    {
        $this->logger->info('Generating Normal tournament', [
            'tournament_id' => $tournament->getId()
        ]);

        $teams = $this->getTopTeams($tournament->getCompetition());
        $teamsCount = count($teams);

        if ($teamsCount < 4) {
            throw new \Exception("Il faut au moins 4 équipes pour générer un tournoi éliminatoire");
        }

        $timeSlots = $this->generateTimeSlots($tournament, 7); // 4 quarts + 2 demis + 1 finale
        $fields = $this->getFields($tournament->getCompetition());

        // Organiser les matches par phases
        $currentSlot = 0;
        
        // Quarts de finale (4 matches)
        shuffle($teams);
        for ($i = 0; $i < 4; $i++) {
            $encounter = $this->createEncounter(
                $tournament,
                $teams[$i * 2],
                $teams[$i * 2 + 1],
                $timeSlots[$currentSlot],
                $fields[$currentSlot % count($fields)]
            );
            $encounter->setRound(1);
            $this->entityManager->persist($encounter);
            $currentSlot++;
        }

        // Demi-finales (2 matches)
        for ($i = 0; $i < 2; $i++) {
            $encounter = $this->createEncounter(
                $tournament,
                null, // Will be determined after quarter finals
                null,
                $timeSlots[$currentSlot],
                $fields[$currentSlot % count($fields)]
            );
            $encounter->setRound(2);
            $this->entityManager->persist($encounter);
            $currentSlot++;
        }

        // Finale (1 match)
        $final = $this->createEncounter(
            $tournament,
            null, // Will be determined after semi finals
            null,
            $timeSlots[$currentSlot],
            $fields[$currentSlot % count($fields)]
        );
        $final->setRound(3);
        $this->entityManager->persist($final);

        $this->entityManager->flush();

        $this->logger->info('Normal tournament generation completed', [
            'tournament_id' => $tournament->getId()
        ]);
    }

    private function createEncounter(Tournament $tournament, ?Team $teamBlue, ?Team $teamGreen, TimeSlot $timeSlot, Field $field): Encounter
    {
        $encounter = new Encounter();
        $encounter->setTournament($tournament)
            ->setTimeSlot($timeSlot)
            ->setField($field)
            ->setState('PROGRAMMEE');

        if ($teamBlue) {
            $encounter->setTeamBlue($teamBlue);
        }
        if ($teamGreen) {
            $encounter->setTeamGreen($teamGreen);
        }

        return $encounter;
    }

    private function generateTimeSlots(Tournament $tournament, int $numberOfSlots): array
    {
        $startDate = $tournament->getCompetition()->getCmpDateBegin();
        $endDate = $tournament->getCompetition()->getCmpDateEnd();
        $duration = 30; // 30 minutes par match
        $break = 10; // 10 minutes de pause entre les matchs

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

            $this->entityManager->persist($timeSlot);
            $timeSlots[] = $timeSlot;

            $currentTime = (clone $slotEnd)->modify("+{$break} minutes");
        }

        return $timeSlots;
    }

    private function getTopTeams($competition, $limit = 32): array
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb->select('t')
            ->from(Team::class, 't')
            ->where('t.competition = :competition')
            ->setParameter('competition', $competition)
            ->orderBy('t.score', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}