<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\TTeamTem;
use App\Form\TTeamTemType;
use App\Repository\TTeamTemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TeamController extends AbstractController
{
    // #[Route('/team', name: 'app_team')]
    #[Route('/team', name: 'app_team')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        TTeamTemRepository $teamRepository
    ): Response {
        $user = $this->getUser();

        // Récupère l'équipe existante de l'utilisateur
        $team = $teamRepository->findOneBy(['user' => $user]);
        $team_created = $team !== null;

        // Initialise le formulaire
        $newTeam = new TTeamTem();
        $newTeam->setUser($user);

        $form = $this->createForm(TTeamTemType::class, $newTeam);
        $form->handleRequest($request);

        // Gère la soumission du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$team_created) {
                $entityManager->persist($newTeam);
                $entityManager->flush();

                $this->addFlash('success', 'Équipe créée avec succès !');
                return $this->redirectToRoute('app_team');
            } else {
                $this->addFlash('error', 'Vous ne pouvez créer qu\'une seule équipe.');
            }
        }

        return $this->render('/team/index.html.twig', [
            'team' => $team,
            'team_created' => $team_created,
            'form' => $form->createView(),
        ]); 

    }

}
