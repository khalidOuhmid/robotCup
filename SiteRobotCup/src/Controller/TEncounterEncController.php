<?php

namespace App\Controller;

use App\Entity\TEncounterEnc;
use App\Form\TEncounterEncType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/t/encounter/enc')]
final class TEncounterEncController extends AbstractController
{
    #[Route(name: 'app_t_encounter_enc_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $tEncounterEncs = $entityManager
            ->getRepository(TEncounterEnc::class)
            ->findAll();

        return $this->render('t_encounter_enc/index.html.twig', [
            't_encounter_encs' => $tEncounterEncs,
        ]);
    }

    #[Route('/new', name: 'app_t_encounter_enc_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tEncounterEnc = new TEncounterEnc();
        $form = $this->createForm(TEncounterEncType::class, $tEncounterEnc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tEncounterEnc);
            $entityManager->flush();

            return $this->redirectToRoute('app_t_encounter_enc_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('t_encounter_enc/new.html.twig', [
            't_encounter_enc' => $tEncounterEnc,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_t_encounter_enc_show', methods: ['GET'])]
    public function show(TEncounterEnc $tEncounterEnc): Response
    {
        return $this->render('t_encounter_enc/show.html.twig', [
            't_encounter_enc' => $tEncounterEnc,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_t_encounter_enc_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TEncounterEnc $tEncounterEnc, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TEncounterEncType::class, $tEncounterEnc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_t_encounter_enc_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('t_encounter_enc/edit.html.twig', [
            't_encounter_enc' => $tEncounterEnc,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_t_encounter_enc_delete', methods: ['POST'])]
    public function delete(Request $request, TEncounterEnc $tEncounterEnc, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tEncounterEnc->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tEncounterEnc);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_t_encounter_enc_index', [], Response::HTTP_SEE_OTHER);
    }
}
