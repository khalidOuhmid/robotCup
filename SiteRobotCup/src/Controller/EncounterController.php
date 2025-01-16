<?php

namespace App\Controller;

use App\Entity\Encounter;
use App\Form\EncounterForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/t/encounter/enc')]
final class EncounterController extends AbstractController
{
    #[Route(name: 'app_t_encounter_enc_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $Encounters = $entityManager
            ->getRepository(Encounter::class)
            ->findAll();

        return $this->render('t_encounter_enc/index.html.twig', [
            't_encounter_encs' => $Encounters,
        ]);
    }

    #[Route('/new', name: 'app_t_encounter_enc_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $Encounter = new Encounter();
        $form = $this->createForm(EncounterForm::class, $Encounter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($Encounter);
            $entityManager->flush();

            return $this->redirectToRoute('app_t_encounter_enc_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('t_encounter_enc/new.html.twig', [
            't_encounter_enc' => $Encounter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_t_encounter_enc_show', methods: ['GET'])]
    public function show(Encounter $Encounter): Response
    {
        return $this->render('t_encounter_enc/show.html.twig', [
            't_encounter_enc' => $Encounter,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_t_encounter_enc_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Encounter $Encounter, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EncounterForm::class, $Encounter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_t_encounter_enc_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('t_encounter_enc/edit.html.twig', [
            't_encounter_enc' => $Encounter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_t_encounter_enc_delete', methods: ['POST'])]
    public function delete(Request $request, Encounter $Encounter, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$Encounter->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($Encounter);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_t_encounter_enc_index', [], Response::HTTP_SEE_OTHER);
    }
}


