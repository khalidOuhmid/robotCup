<?php
// src/Service/TournamentGenerator.php
namespace App\Service;

use App\Entity\Tournament;
use App\Entity\Team;
use App\Entity\Encounter;
use Doctrine\ORM\EntityManagerInterface;

class TournamentGenerator
{
    private $entityManager;
    

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function generateTournamentBracket(Tournament $tournament): void
    {
        // Récupérer les équipes du championnat, triées par performance
        $teams = $this->getTopTeams($tournament->getCompetition(), 32);
        $numberOfTeams = count($teams);

        if ($numberOfTeams < 2) {
            throw new \Exception("Pas assez d'équipes pour générer un tournoi");
        }

        // Créer les matches du premier tour
        $matches = [];
        for ($i = 0; $i < $numberOfTeams / 2; $i++) {
            $matches[] = [
                'blue' => $teams[$i],
                'green' => $teams[$numberOfTeams - 1 - $i]
            ];
        }

        // Créer les rencontres dans la base de données
        foreach ($matches as $match) {
            $encounter = new Encounter();
            $encounter->setTournament($tournament);
            $encounter->setTeamBlue($match['blue']);
            $encounter->setTeamGreen($match['green']);
            $encounter->setState('PROGRAMMEE');
            // Définir les dates selon votre logique
            
            $this->entityManager->persist($encounter);
        }

        $this->entityManager->flush();
    }

    public function generateSwissTournament(Tournament $tournament, int $rounds): void
    {
        $teams = $this->getTopTeams($tournament->getCompetition());
        $numberOfTeams = count($teams);

        if ($numberOfTeams < 2) {
            throw new \Exception("Pas assez d'équipes pour générer un tournoi");
        }

        // La contrainte de la base de données limite à 16 rencontres au total
        $maxRounds = min($rounds, 16);
        
        // Générer les premiers matchs aléatoirement
        shuffle($teams);
        
        $encountersCreated = 0;
        for ($round = 0; $round < $maxRounds && $encountersCreated < 16; $round++) {
            for ($i = 0; $i < floor($numberOfTeams / 2) && $encountersCreated < 16; $i++) {
                $encounter = new Encounter();
                $encounter->setTournament($tournament)
                    ->setTeamBlue($teams[$i * 2])
                    ->setTeamGreen($teams[$i * 2 + 1])
                    ->setState('PROGRAMMEE');
                
                $this->entityManager->persist($encounter);
                $encountersCreated++;
            }
        }

        $this->entityManager->flush();
    }

    public function generateTournament(Tournament $tournament): void
    {
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
    }

    private function generateDutchTournament(Tournament $tournament): void
    {
        $teams = $this->getTopTeams($tournament->getCompetition());
        $numberOfTeams = count($teams);
        
        if ($numberOfTeams < 2) {
            throw new \Exception("Pas assez d'équipes pour générer un tournoi");
        }

        // Système hollandais: les équipes sont appariées selon leur classement
        $encountersCreated = 0;
        for ($i = 0; $i < floor($numberOfTeams / 2) && $encountersCreated < 16; $i++) {
            $encounter = new Encounter();
            $encounter->setTournament($tournament)
                ->setTeamBlue($teams[$i])
                ->setTeamGreen($teams[$numberOfTeams - 1 - $i])
                ->setState('PROGRAMMEE');
            
            $this->entityManager->persist($encounter);
            $encountersCreated++;
        }

        $this->entityManager->flush();
    }

    private function generateNormalTournament(Tournament $tournament): void
    {
        $teams = $this->getTopTeams($tournament->getCompetition());
        $numberOfTeams = count($teams);
        
        if ($numberOfTeams < 2) {
            throw new \Exception("Pas assez d'équipes pour générer un tournoi");
        }

        // Système normal: appariement aléatoire des équipes
        shuffle($teams);
        $encountersCreated = 0;
        
        for ($i = 0; $i < floor($numberOfTeams / 2) && $encountersCreated < 16; $i++) {
            $encounter = new Encounter();
            $encounter->setTournament($tournament)
                ->setTeamBlue($teams[$i * 2])
                ->setTeamGreen($teams[$i * 2 + 1])
                ->setState('PROGRAMMEE');
            
            $this->entityManager->persist($encounter);
            $encountersCreated++;
        }

        $this->entityManager->flush();
    }

    private function getTopTeams($competition, $limit = 32): array
    {
        // Logique pour récupérer les meilleures équipes du championnat
        // basée sur les scores, victoires, etc.
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