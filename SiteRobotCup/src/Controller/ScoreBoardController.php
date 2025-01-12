<?php

namespace App\Controller;

use App\Entity\Team;
use App\Repository\TeamRepository;
use App\Entity\Encounter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for managing and displaying the scoreboard.
 */
class ScoreBoardController extends AbstractController
{
    /**
     * Displays the scoreboard with teams sorted by score and victories.
     *
     * @param TeamRepository $teamRepository Repository for team operations
     * @return Response Rendered scoreboard view
     */
    #[Route('/table/score', name: 'app_table_score', methods: ['GET'])]
    public function index(TeamRepository $teamRepository): Response
    {
        $teams = $teamRepository->findByOrderedByScore();

        return $this->render('table_score/index.html.twig', [
            'teams' => $teams,
        ]);
    }
}
