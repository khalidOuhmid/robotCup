<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageTableauScoresController extends AbstractController
{
    #[Route('/scores', name: 'app_page_tableau_scores')]
    public function index(): Response
    {
        // Exemple de données des équipes
        $teams = [
            ['name' => 'Équipe A', 'matches_played' => 10, 'matches_won' => 7, 'matches_drawn' => 2, 'points' => 2],
            ['name' => 'Équipe B', 'matches_played' => 10, 'matches_won' => 6, 'matches_drawn' => 3, 'points' => 21],
            ['name' => 'Équipe C', 'matches_played' => 10, 'matches_won' => 5, 'matches_drawn' => 4, 'points' => 19],
            // Ajoutez d'autres équipes si nécessaire
        ];

        // Tri des équipes par points dans l'ordre décroissant
        usort($teams, function ($a, $b) {
            return $b['points'] <=> $a['points'];
        });

        // Mise à jour automatique des rangs
        foreach ($teams as $index => &$team) {
            $team['rank'] = $index + 1; // Le rang commence à 1
        }
        unset($team); // Éviter les problèmes de référence après le foreach

        // Passer les données à Twig
        return $this->render('page_tableau_scores/index.html.twig', [
            'teams' => $teams,
        ]);
    }
}
