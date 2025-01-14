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