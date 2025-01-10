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
        // Get teams stats directly from the view
        $teamsData = $teamRepository->findByOrderedByScore();

        // Transform the data for the template
        $teams = array_map(function($data, $index) {
            return [
                'rank' => $index + 1,
                'name' => $data['TEM_NAME'],
                'matches_played' => $data['matches_played'],
                'matches_won' => $data['matches_won'],
                'matches_drawn' => $data['matches_drawn'],
                'matches_lost' => $data['matches_lost'],
                'goals' => $data['total_goals'],
                'points' => $data['total_points'],  // Changed from championship_points to total_points
            ];
        }, $teamsData, array_keys($teamsData));

        // Choose template based on route
        $template = ($_SERVER['REQUEST_URI'] === '/scores') 
            ? 'page_tableau_scores/index.html.twig'
            : 'default/index.html.twig';

        return $this->render($template, [
            'teams' => $teams,
        ]);
    }
}
