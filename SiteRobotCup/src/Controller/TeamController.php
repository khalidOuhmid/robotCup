<?php

namespace App\Controller;

use App\Repository\TTeamTemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TeamController extends AbstractController
{
    // #[Route('/team', name: 'app_team')]
    #[Route('/team', name: 'app_team')]
    public function index(TTeamTemRepository $teamRepository): Response
    {
        $user = $this->getUser();

        $team = $teamRepository->findOneBy(['user' => $user]);

        $teamCreated = $team !== null;

        return $this->render('/team/index.html.twig', [
            'team' => $team,
            'teamCreated' => $teamCreated,
        ]);  

    }
}
