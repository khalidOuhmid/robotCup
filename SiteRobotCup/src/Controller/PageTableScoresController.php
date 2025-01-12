<?php

namespace App\Controller;

use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller for displaying team scores and rankings.
 */
class PageTableScoresController extends AbstractController
{
    /**
     * Displays the scoreboard page with team rankings.
     *
     * @param TeamRepository $teamRepository
     * @param Request $request
     * @return Response
     */
    #[Route('/scores', name: 'app_page_tableau_scores', methods: ['GET'])]
    #[Route('/', name: 'app_default', methods: ['GET'])]
    public function index(
        TeamRepository $teamRepository,
        Request $request
    ): Response {
        $teams = $this->prepareTeamsData($teamRepository);
        $template = $this->determineTemplate($request->getRequestUri());

        return $this->render($template, [
            'teams' => $teams,
        ]);
    }

    /**
     * Prepares team data for display.
     *
     * @param TeamRepository $teamRepository
     * @return array
     */
    private function prepareTeamsData(TeamRepository $teamRepository): array
    {
        $teamsData = $teamRepository->findByOrderedByScore();
        
        return array_map(
            [$this, 'transformTeamData'],
            $teamsData,
            array_keys($teamsData)
        );
    }

    /**
     * Transforms raw team data into display format.
     *
     * @param array $data Raw team data
     * @param int $index Array index for ranking
     * @return array Transformed team data
     */
    private function transformTeamData(array $data, int $index): array
    {
        return [
            'rank' => $index + 1,
            'name' => $data['TEM_NAME'],
            'matches_played' => $data['matches_played'],
            'matches_won' => $data['matches_won'],
            'matches_drawn' => $data['matches_drawn'],
            'matches_lost' => $data['matches_lost'],
            'goals' => $data['total_goals'],
            'points' => $data['total_points'],
        ];
    }

    /**
     * Determines which template to use based on the request URI.
     *
     * @param string $requestUri
     * @return string
     */
    private function determineTemplate(string $requestUri): string
    {
        return $requestUri === '/scores'
            ? 'page_tableau_scores/index.html.twig'
            : 'default/index.html.twig';
    }
}
