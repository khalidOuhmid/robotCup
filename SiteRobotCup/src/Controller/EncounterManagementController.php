<?php

namespace App\Controller;

use App\Entity\Encounter;
use App\Form\EncounterForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller for managing encounters/time slots.
 */
#[IsGranted('ROLE_ADMIN')]
class EncounterManagementController extends AbstractController
{
    /**
     * Displays the encounter calendar and management interface.
     */
    #[Route('/admin/encounters', name: 'app_admin_encounters')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $encounters = $entityManager
            ->getRepository(Encounter::class)
            ->findBy([], ['dateBegin' => 'ASC']);

        return $this->render('admin/encounter/index.html.twig', [
            'encounters' => $encounters
        ]);
    }

    /**
     * Creates a new encounter.
     */
    #[Route('/admin/encounter/new', name: 'app_admin_encounter_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $encounter = new Encounter();
        $form = $this->createForm(EncounterForm::class, $encounter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($encounter);
                $entityManager->flush();
                
                $this->addFlash('success', 'Rencontre créée avec succès');
                return $this->redirectToRoute('app_admin_encounters');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la création de la rencontre');
            }
        }

        return $this->render('admin/encounter/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Edits an existing encounter.
     */
    #[Route('/admin/encounter/{id}/edit', name: 'app_admin_encounter_edit')]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        Encounter $encounter
    ): Response {
        $form = $this->createForm(EncounterForm::class, $encounter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Rencontre modifiée avec succès');
                return $this->redirectToRoute('app_admin_encounters');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la modification de la rencontre');
            }
        }

        return $this->render('admin/encounter/edit.html.twig', [
            'form' => $form->createView(),
            'encounter' => $encounter
        ]);
    }

    /**
     * Deletes an encounter.
     */
    #[Route('/admin/encounter/{id}/delete', name: 'app_admin_encounter_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        EntityManagerInterface $entityManager,
        Encounter $encounter
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$encounter->getId(), $request->request->get('_token'))) {
            try {
                $entityManager->remove($encounter);
                $entityManager->flush();
                $this->addFlash('success', 'Rencontre supprimée avec succès');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la suppression de la rencontre');
            }
        }

        return $this->redirectToRoute('app_admin_encounters');
    }
}
