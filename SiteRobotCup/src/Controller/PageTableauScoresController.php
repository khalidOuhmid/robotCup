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
            ['rank' => 1, 'name' => 'Équipe A', 'matches_played' => 10, 'matches_won' => 7, 'matches_drawn' => 2, 'points' => 3],
            ['rank' => 2, 'name' => 'Équipe B', 'matches_played' => 10, 'matches_won' => 6, 'matches_drawn' => 3, 'points' => 21],
            ['rank' => 3, 'name' => 'Équipe C', 'matches_played' => 10, 'matches_won' => 5, 'matches_drawn' => 4, 'points' => 19],
            // Ajoutez d'autres équipes si nécessaire
        ];

        // Passer les données à Twig
        return $this->render('page_tableau_scores/index.html.twig', [
            'teams' => $teams,
        ]);
    }
}
