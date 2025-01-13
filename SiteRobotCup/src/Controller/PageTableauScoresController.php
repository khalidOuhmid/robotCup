<?php

namespace App\Controller;

use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageTableauScoresController extends AbstractController
{
    #[Route('/tableau-scores', name: 'app_page_tableau_scores')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $limit = 10; // Teams per page
        $teamRepository = $entityManager->getRepository(Team::class);
        
        // Get total teams count
        $totalTeams = $teamRepository->count([]);
        
        // Handle empty database case
        if ($totalTeams === 0) {
            return $this->render('page_tableau_scores/index.html.twig', [
                'teams' => [],
                'page' => 1,
                'totalPages' => 1
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

        $teams = $teamRepository->findBy(
            [], 
            ['name' => 'ASC'],
            $limit,
            $offset
        );

        return $this->render('page_tableau_scores/index.html.twig', [
            'teams' => $teams,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }
}
