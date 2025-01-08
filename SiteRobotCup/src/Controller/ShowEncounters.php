<?php

namespace App\Controller;

use App\Entity\TEncounterEnc;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ShowEncounters extends AbstractController
{
    #[Route('/encounters', name: 'app_encounters')]
    public function showEncounters(EntityManagerInterface $entityManager): Response
    {
        $encounters = $entityManager->getRepository(TEncounterEnc::class)->findAll();

        return $this->render('encounters/index.html.twig', [
            'encounters' => $encounters
        ]);
    }
}
