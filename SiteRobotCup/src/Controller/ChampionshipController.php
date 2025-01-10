<?php
namespace App\Controller;

use App\Service\ChampionshipPlanner;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChampionshipController extends AbstractController
{
    private ChampionshipPlanner $planner;

    public function __construct(ChampionshipPlanner $planner)
    {
        $this->planner = $planner;
    }

    #[Route('/championship/generate', name: 'generate_championship')]
public function generate(): Response
{
    $teams = $this->planner->getTeams();
    $championshipId = 1; // Ou utilisez un ID dynamique si nécessaire
    $schedule = $this->planner->generateSchedule($teams, $championshipId);
    $this->planner->saveSchedule($schedule);

    // Utilisez getMatches() pour obtenir les rencontres dans l'ordre correct
    $matches = $this->planner->getMatches();

    return $this->render('championship/generated.html.twig', [
        'total_matches' => count($matches),
        'teams' => $teams,
        'matches' => $matches,
    ]);
}


    public function viewMatches(): Response
    {
        $matches = $this->planner->getMatches();
    
        return $this->render('championship/generated.html.twig', [
            'matches' => $matches,
        ]);
    }
    
}

