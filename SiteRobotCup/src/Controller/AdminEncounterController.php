<?php

namespace App\Controller;

use App\Form\GenerateChampionshipMatchesType;
use App\Service\ChampionshipScheduler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminEncounterController extends AbstractController
{
    #[Route('/admin/encounter/generateChampionship', name: 'app_admin_encounter_generate_championship')]
    public function generateChampionship(
        Request $request, 
        ChampionshipScheduler $scheduler
    ): Response {
        $form = $this->createForm(GenerateChampionshipMatchesType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            // Transformer les données du formulaire en format attendu par le scheduler
            $timeSlots = array_map(function($timeSlot) {
                return [
                    'start' => $timeSlot['startTime'],
                    'end' => $timeSlot['endTime'],
                    'count' => $timeSlot['matchCount']
                ];
            }, $data['timeSlots']);

            try {
                $scheduler->generateMatches(
                    $data['championship']->getId(),
                    $timeSlots,
                    $data['field']->getId()
                );

                $this->addFlash('success', 'Les rencontres ont été générées avec succès.');
                return $this->redirectToRoute('app_admin_encounters');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la génération des rencontres : ' . $e->getMessage());
            }
        }

        return $this->render('admin/encounter/generate_championship.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}