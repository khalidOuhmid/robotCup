<?php
// src/Controller/Admin/CompetitionController.php
namespace App\Controller;

use App\Entity\Competition;
use App\Entity\Championship;
use App\Entity\Tournament;
use App\Form\CompetitionType;
use App\Service\TournamentGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompetitionController extends AbstractController
{
    #[Route('/admin/competition/new', name: 'app_admin_competition_new')]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager,
        TournamentGenerator $tournamentGenerator
    ): Response {
        $competition = new Competition();
        $form = $this->createForm(CompetitionType::class, $competition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Création du championnat
            $championship = new Championship();
            $championship->setCompetition($competition);
            
            
            $entityManager->persist($competition);
            $entityManager->persist($championship);

            // Si le tournoi est demandé
            if ($form->get('includeTournament')->getData()) {
                $tournament = new Tournament();
                $tournament->setCompetition($competition);
                $tournament->setIncludeThirdPlace($form->get('includeThirdPlace')->getData());
                
                $entityManager->persist($tournament);
            }

            $entityManager->flush();

            $this->addFlash('success', 'La compétition a été créée avec succès.');
            return $this->redirectToRoute('app_default');
        }

        return $this->render('admin/competition/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}