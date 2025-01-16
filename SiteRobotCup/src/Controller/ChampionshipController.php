<?php

namespace App\Controller;

use App\Entity\Championship;
use App\Entity\Encounter;
use App\Entity\Team;
use App\Service\ChampionshipScheduler;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/championship', name: 'app_championship_')]
final class ChampionshipController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ChampionshipScheduler $championshipScheduler,
        private LoggerInterface $logger
    ) {}

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $championships = $this->entityManager
            ->getRepository(Championship::class)
            ->findBy([], ['id' => 'DESC']);

        return $this->render('championship/index.html.twig', [
            'championships' => $championships
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Championship $championship): Response
    {
        $encounters = $this->entityManager
            ->getRepository(Encounter::class)
            ->findBy(['championship' => $championship], ['id' => 'ASC']);

        return $this->render('championship/show.html.twig', [
            'championship' => $championship,
            'encounters' => $encounters
        ]);
    }

    #[Route('/{id}/generate', name: 'generate', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function generate(Championship $championship): Response
    {
        try {
            $this->logger->info('Starting championship generation', [
                'championship_id' => $championship->getId(),
                'type' => $championship->getType(),
                'competition_id' => $championship->getCompetition()->getId()
            ]);

            $teams = $this->entityManager->getRepository(Team::class)
                ->findBy(['competition' => $championship->getCompetition()]);

            $this->logger->info('Found teams', ['count' => count($teams)]);

            if (empty($teams)) {
                throw new \Exception("Aucune équipe n'est inscrite dans cette compétition");
            }

            $this->championshipScheduler->generateChampionship($championship);
            $this->addFlash('success', 'Les rencontres ont été générées avec succès.');
        } catch (\Exception $e) {
            $this->logger->error('Error generating encounters', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->addFlash('error', 'Erreur lors de la génération des rencontres: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_championship_show', ['id' => $championship->getId()]);
    }
}
