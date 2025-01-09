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
        try {
            // Étape 1 : Récupérer les équipes
            $teams = $this->planner->getTeams();

            // Étape 2 : Spécifier l'ID du championnat
            $championshipId = 1; // Remplacez par un ID dynamique si nécessaire

            // Étape 3 : Générer le planning
            $schedule = $this->planner->generateSchedule($teams, $championshipId);

            // Étape 4 : Sauvegarder le planning
            $this->planner->saveSchedule($schedule);

            return $this->render('championship/generated.html.twig', [
                'total_matches' => count($schedule),
                'teams' => $teams, // Si vous souhaitez afficher les équipes
            ]);
        } catch (\Exception $e) {
            return new Response('Erreur : ' . $e->getMessage(), 500);
        }
    }
}
