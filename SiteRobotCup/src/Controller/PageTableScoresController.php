<?php

namespace App\Controller;

use App\Repository\TTeamTemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PageTableScoresController extends AbstractController
{
    #[Route('/scores', name: 'app_page_tableau_scores')]
    #[Route('/', name: 'app_default')]
    public function index(Request $request, TTeamTemRepository $teamRepository): Response
    {
        // Nombre d'éléments par page
        $itemsPerPage = 1;

        // Récupérer la page actuelle, défaut à 1 si non définie
        $page = $request->query->getInt('page', 1);

        // Récupérer les équipes, paginer les résultats
        $teamsData = $teamRepository->findBy([], ['score' => 'DESC'], $itemsPerPage, ($page - 1) * $itemsPerPage);

        // Total des équipes pour la pagination
        $totalTeams = count($teamRepository->findAll());
        
        // Calcul du nombre total de pages
        $totalPages = ceil($totalTeams / $itemsPerPage);

        // Transformez les équipes en un tableau de données à afficher
        $teams = [];
        foreach ($teamsData as $index => $team) {
            // Calcul des matchs joués, gagnés, nuls, perdus (logique identique à votre code précédent)
            $matchesPlayed = count($team->getEncountersAsBlue()) + count($team->getEncountersAsGreen());
            $matchesWon = 0;
            $matchesDrawn = 0;
            $matchesLost = 0;

            // Compter les victoires, nuls, et défaites des rencontres en tant que bleu et vert
            foreach ($team->getEncountersAsBlue() as $encounter) {
                if ($encounter->getScoreBlue() > $encounter->getScoreGreen()) {
                    $matchesWon++;
                } elseif ($encounter->getScoreBlue() === $encounter->getScoreGreen()) {
                    $matchesDrawn++;
                } else {
                    $matchesLost++;
                }
            }

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
                'rank' => ($page - 1) * $itemsPerPage + $index + 1, // Calculer le rang en fonction de la page
                'name' => $team->getName(),
                'matches_played' => $matchesPlayed,
                'matches_won' => $matchesWon,
                'matches_drawn' => $matchesDrawn,
                'matches_lost' => $matchesLost,
                'points' => $team->getScore(),
            ];
        }

        // Retourner le template avec les informations de pagination et les équipes
        return $this->render('page_tableau_scores/index.html.twig', [
            'teams' => $teams,
            'page' => $page,
            'totalPages' => $totalPages,
        ]);
    }
}