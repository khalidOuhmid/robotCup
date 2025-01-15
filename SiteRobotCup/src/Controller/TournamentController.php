<?php

namespace App\Controller;

use App\Entity\Tournament;
use App\Entity\Encounter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tournament', name: 'app_tournament_')]
final class TournamentController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('', name: 'index', methods: ['GET'])]  // Changed from '/' to ''
    public function index(): Response
    {
        $tournaments = $this->entityManager
            ->getRepository(Tournament::class)
            ->findBy([], ['id' => 'DESC']);

        return $this->render('tournament/index.html.twig', [
            'tournaments' => $tournaments
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Tournament $tournament): Response
    {
        // Charger les rencontres associées au tournoi
        $encounters = $this->entityManager
            ->getRepository(Encounter::class)
            ->findBy(['tournament' => $tournament], ['id' => 'ASC']);

        return $this->render('tournament/show.html.twig', [
            'tournament' => $tournament,
            'encounters' => $encounters
        ]);
    }
}
