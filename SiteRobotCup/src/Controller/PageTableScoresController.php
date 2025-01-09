<?php

namespace App\Controller;

use App\Repository\TTeamTemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageTableScoresController extends AbstractController
{
    #[Route('/scores', name: 'app_page_tableau_scores')]
    #[Route('/', name: 'app_default')]
    public function index(TTeamTemRepository $teamRepository): Response
    {
        // Get teams sorted by score
        $teamsData = $teamRepository->findBy([], ['score' => 'DESC']);

        // Transform team entities into array with required data
        $teams = [];
        foreach ($teamsData as $index => $team) {
            // Calculate matches data from encounters
            $matchesPlayed = count($team->getEncountersAsBlue()) + count($team->getEncountersAsGreen());
            $matchesWon = 0;
            $matchesDrawn = 0;
            $matchesLost = 0; // Nouveau compteur pour les matchs perdus
            
            // Count wins, draws, and losses for blue team encounters
            foreach ($team->getEncountersAsBlue() as $encounter) {
                if ($encounter->getScoreBlue() > $encounter->getScoreGreen()) {
                    $matchesWon++;
                } elseif ($encounter->getScoreBlue() === $encounter->getScoreGreen()) {
                    $matchesDrawn++;
                } else {
                    $matchesLost++;
                }
            }
            
            // Count wins, draws, and losses for green team encounters
            foreach ($team->getEncountersAsGreen() as $encounter) {
                if ($encounter->getScoreGreen() > $encounter->getScoreBlue()) {
                    $matchesWon++;
                } elseif ($encounter->getScoreGreen() === $encounter->getScoreBlue()) {
                    $matchesDrawn++;
                } else {
                    $matchesLost++;
                }
            }

            $teams[] = [
                'rank' => $index + 1,
                'name' => $team->getName(),
                'matches_played' => $matchesPlayed,
                'matches_won' => $matchesWon,
                'matches_drawn' => $matchesDrawn,
                'matches_lost' => $matchesLost, // Ajout des matchs perdus dans le tableau
                'points' => $team->getScore(),
            ];
        }

        // Condition pour déterminer quel template rendre
        if ($_SERVER['REQUEST_URI'] === '/scores') {
            // Rendre le template 'page_tableau_scores/index.html.twig'
            return $this->render('page_tableau_scores/index.html.twig', [
                'teams' => $teams,
            ]);
        } else {
            // Rendre le template 'default/index.html.twig'
            return $this->render('default/index.html.twig', [
                'teams' => $teams,
            ]);
        } 
    }
}
