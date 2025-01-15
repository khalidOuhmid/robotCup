<?php

namespace App\Controller;

use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Team;

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

        // see the user's teams if logged in
        $user = $this->getUser();

        if ($user) {
            $userTeams = $teamRepository->findBy(['user' => $user]);
            $userTeamIds = array_map(fn(Team $team) => $team->getId(), $userTeams);
        } else {
            $userTeamIds = [];
        }

        return $this->render($template, [
            'teams' => $teams,
            'userTeamIds' => $userTeamIds,
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
            'id' => $data['team_id'],
            'rank' => $index + 1,
            'name' => $data['team_name'],
            'matches_played' => $data['total_matches'],
            'matches_won' => $data['total_wins'],
            'matches_drawn' => $data['total_draws'],
            'matches_lost' => $data['total_losses'],
            'goals' => $data['average_score'],
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
