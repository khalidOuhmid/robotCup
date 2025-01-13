<?php

namespace App\Controller;

use App\Service\ChampionshipPlanner;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for managing championship operations.
 */
class ChampionshipController extends AbstractController
{
    private const DEFAULT_CHAMPIONSHIP_ID = 1;

    /**
     * @var ChampionshipPlanner
     */
    private ChampionshipPlanner $planner;

    /**
     * Constructor.
     *
     * @param ChampionshipPlanner $planner Service for planning championships
     */
    public function __construct(ChampionshipPlanner $planner)
    {
        $this->planner = $planner;
    }

    /**
     * Generates a new championship schedule.
     *
     * @return Response
     */
    #[Route('/championship/generate', name: 'generate_championship', methods: ['GET'])]
    public function generate(): Response
    {
        $teams = $this->planner->getTeams();
        $schedule = $this->generateChampionshipSchedule($teams);
        $matches = $this->planner->getMatches();

        return $this->renderChampionshipSchedule($matches, $teams);
    }

    /**
     * Displays all championship matches.
     *
     * @return Response
     */
    #[Route('/championship/matches', name: 'view_championship_matches', methods: ['GET'])]
    public function viewMatches(): Response
    {
        $matches = $this->planner->getMatches();
        
        return $this->render('championship/generated.html.twig', [
            'matches' => $matches,
        ]);
    }

    /**
     * Generates the championship schedule for given teams.
     *
     * @param array $teams
     * @return array
     */
    private function generateChampionshipSchedule(array $teams): array
    {
        $schedule = $this->planner->generateSchedule(
            $teams,
            self::DEFAULT_CHAMPIONSHIP_ID
        );
        
        $this->planner->saveSchedule($schedule);
        
        return $schedule;
    }

    /**
     * Renders the championship schedule view.
     *
     * @param array $matches
     * @param array $teams
     * @return Response
     */
    private function renderChampionshipSchedule(
        array $matches,
        array $teams
    ): Response {
        return $this->render('championship/generated.html.twig', [
            'total_matches' => count($matches),
            'teams' => $teams,
            'matches' => $matches,
        ]);
    }
}
