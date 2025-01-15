<?php

namespace App\Controller;

use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\TeamRepository;

class PageTableauScoresController extends AbstractController
{
    #[Route('/tableau-scores', name: 'app_page_tableau_scores')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Handle user not logged in
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $limit = 10; // Teams per page
        $teamRepository = $entityManager->getRepository(Team::class);

        // Get total teams count
        $totalTeams = $teamRepository->count([]);

        // Handle empty database case
        if ($totalTeams === 0) {
            return $this->render('page_tableau_scores/index.html.twig', [
                'teams' => [],
                'page' => 1,
                'totalPages' => 1,
                'userTeamIds' => [],
            ]);
        }

        $totalPages = max(1, ceil($totalTeams / $limit));
        $page = $request->query->getInt('page', 1);
        $page = max(1, min($page, $totalPages));

        $offset = ($page - 1) * $limit;
        if ($offset >= $totalTeams) {
            $offset = 0;
            $page = 1;
        }

        $teams = $teamRepository->findBy([], ['name' => 'ASC'], $limit, $offset);

        $teams = $this->prepareTeamsData($teamRepository);

        // Fetch user teams
        $userTeams = $teamRepository->findBy(['user' => $user]);
        $userTeamIds = array_map(fn(Team $team) => $team->getId(), $userTeams);

        return $this->render('page_tableau_scores/index.html.twig', [
            'teams' => $teams,
            'page' => $page,
            'totalPages' => $totalPages,
            'userTeamIds' => $userTeamIds,
        ]);
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
}
