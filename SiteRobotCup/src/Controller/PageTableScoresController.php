<?php

namespace App\Controller;

use App\Repository\TTeamTemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageTableScoresController extends AbstractController
{
    #[Route('/scores', name: 'app_page_tableau_scores')]
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
            
            // Count wins and draws for blue team encounters
            foreach ($team->getEncountersAsBlue() as $encounter) {
                if ($encounter->getScoreBlue() > $encounter->getScoreGreen()) {
                    $matchesWon++;
                } elseif ($encounter->getScoreBlue() === $encounter->getScoreGreen()) {
                    $matchesDrawn++;
                }
            }
            
            // Count wins and draws for green team encounters
            foreach ($team->getEncountersAsGreen() as $encounter) {
                if ($encounter->getScoreGreen() > $encounter->getScoreBlue()) {
                    $matchesWon++;
                } elseif ($encounter->getScoreGreen() === $encounter->getScoreBlue()) {
                    $matchesDrawn++;
                }
            }

            $teams[] = [
                'rank' => $index + 1,
                'name' => $team->getName(),
                'matches_played' => $matchesPlayed,
                'matches_won' => $matchesWon,
                'matches_drawn' => $matchesDrawn,
                'points' => $team->getScore()
            ];
        }

        return $this->render('page_tableau_scores/index.html.twig', [
            'teams' => $teams,
        ]);
    }
}
