<?php

namespace App\Controller;

use App\Entity\Tournament;
use App\Entity\Encounter;
use App\Entity\Team;
use App\Service\TournamentGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tournament', name: 'app_tournament_')]
final class TournamentController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TournamentGenerator $tournamentGenerator,
        private LoggerInterface $logger
    ) {}

    #[Route('', name: 'index', methods: ['GET'])]  
    public function index(): Response
    {
        $tournaments = $this->entityManager->getRepository(Tournament::class)->findAll();

        return $this->render('tournament/index.html.twig', [
            'tournaments' => $tournaments
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Tournament $tournament): Response
    {
        return $this->render('tournament/show.html.twig', [
            'tournament' => $tournament
        ]);
    }

    #[Route('/{id}/generate', name: 'generate', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function generate(Tournament $tournament): Response
    {
        try {
            $this->logger->info('Starting tournament generation', [
                'tournament_id' => $tournament->getId(),
                'type' => $tournament->getType(),
                'competition_id' => $tournament->getCompetition()->getId()
            ]);

            $teams = $this->entityManager->getRepository(Team::class)
                ->findBy(['competition' => $tournament->getCompetition()]);
            
            $this->logger->info('Found teams', ['count' => count($teams)]);
            
            if (empty($teams)) {
                throw new \Exception("Aucune équipe n'est inscrite dans cette compétition");
            }

            $this->tournamentGenerator->generateTournament($tournament);
            $this->entityManager->flush(); // Ensure changes are persisted
            $this->addFlash('success', 'Les rencontres ont été générées avec succès.');
        } catch (\Exception $e) {
            $this->logger->error('Error generating encounters', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->addFlash('error', 'Erreur lors de la génération des rencontres: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_tournament_show', ['id' => $tournament->getId()]);
    }
}
