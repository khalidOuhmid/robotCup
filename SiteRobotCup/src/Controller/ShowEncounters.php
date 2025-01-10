<?php

namespace App\Controller;

use App\Entity\TEncounterEnc;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse; 
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ShowEncounters extends AbstractController
{
    #[Route('/encounters/{page}', name: 'app_encounters', requirements: ['page' => '\d+'], defaults: ['page' => 1])]
    public function showEncounters(int $page, EntityManagerInterface $entityManager): Response
    {
        $limit = 10 ; // Nombre d'éléments par page
        $repository = $entityManager->getRepository(TEncounterEnc::class);

        // Total des rencontres
        $totalEncounters = $repository->count([]);

        // Calcul du nombre total de pages
        $totalPages = (int) ceil($totalEncounters / $limit);

        // Validation de la page demandée
        $page = max(1, min($page, $totalPages)); // Correction pour éviter les pages négatives ou supérieures au total

        // Récupération des rencontres pour la page actuelle
        $offset = ($page - 1) * $limit;
        $encounters = $repository->findBy([], null, $limit, $offset);

        // Rendu de la vue
        return $this->render('encounters/index.html.twig', [
            'encounters' => $encounters,
            'page' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/encounters/export', name: 'app_encounters_export')]
    public function exportEncounters(EntityManagerInterface $entityManager): JsonResponse
    {
        $repository = $entityManager->getRepository(TEncounterEnc::class);

        // Récupérer toutes les rencontres
        $encounters = $repository->findAll();

        // Préparer les données pour l'export
        $data = [];
        foreach ($encounters as $index => $encounter) {
            $data[] = [
                'championshipId' => $encounter->getChampionship()->getId(),
                'teamBlue' => $encounter->getTeamBlue()->getName(),
                'scoreBlue' => $encounter->getScoreBlue(),
                'scoreGreen' => $encounter->getScoreGreen(),
                'teamGreen' => $encounter->getTeamGreen()->getName(),
                'state' => $encounter->getState(),
            ];
        }

        // Encodage des données en JSON
        $jsonData = json_encode($data, JSON_PRETTY_PRINT);

        // Créer une réponse JSON
        $response = new JsonResponse($jsonData, 200, [], true);

        // Configurer l'en-tête pour le téléchargement
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'rencontres.json'
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
