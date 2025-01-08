<?php

namespace App\Controller;

use App\Entity\TEncounterEnc;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowEncounters extends AbstractController
{
    #[Route('/encounters/{page}', name: 'app_encounters', requirements: ['page' => '\d+'], defaults: ['page' => 1])]
    public function showEncounters(int $page, EntityManagerInterface $entityManager): Response
    {
        $limit = 1; // Nombre d'éléments par page
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
}

