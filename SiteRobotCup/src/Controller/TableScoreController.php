<?php

namespace App\Controller;

use App\Repository\TTeamTemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class TableScoreController extends AbstractController
{
    #[Route('/table/score', name: 'app_table_score')]
    public function index(TTeamTemRepository $teamRepository): Response
    {
        $teams = $teamRepository->findAll();
        
        // Sort teams by calculated score and victories
        usort($teams, function($a, $b) {
            $scoreComparison = $b->calculateScore() - $a->calculateScore();
            if ($scoreComparison === 0) {
                return $b->getVictoryCount() - $a->getVictoryCount();
            }
            return $scoreComparison;
        });

        return $this->render('table_score/index.html.twig', [
            'teams' => $teams,
        ]);
    }
}
