<?php

namespace App\Controller;

use App\Entity\Field;
use App\Repository\FieldRepository;
use App\Repository\CompetitionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin')]
class AdminFieldsController extends AbstractController
{
    #[Route('/fields', name: 'app_admin_fields', methods: ['GET'])]
    public function index(Request $request, FieldRepository $fieldRepository, CompetitionRepository $competitionRepository): Response
    {
        return $this->render('admin/fields/index.html.twig', [
            'fields' => $fieldRepository->findAll(),
            'competitions' => $competitionRepository->findAll(),
        ]);
    }

    #[Route('/fields/new', name: 'app_admin_fields_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CompetitionRepository $competitionRepository): Response
    {
        if ($request->isMethod('POST')) {
            $field = new Field();
            $field->setName($request->request->get('name'));
            $competition = $competitionRepository->find($request->request->get('competition'));
            $field->setCompetition($competition);

            $entityManager->persist($field);
            $entityManager->flush();

            $this->addFlash('success', 'Terrain créé avec succès');
            return $this->redirectToRoute('app_admin_fields');
        }

        return $this->render('admin/fields/new.html.twig', [
            'competitions' => $competitionRepository->findAll(),
        ]);
    }

    #[Route('/fields/{id}/edit', name: 'app_admin_fields_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Field $field, EntityManagerInterface $entityManager, CompetitionRepository $competitionRepository): Response
    {
        if ($request->isMethod('POST')) {
            $field->setName($request->request->get('name'));
            $competition = $competitionRepository->find($request->request->get('competition'));
            $field->setCompetition($competition);

            $entityManager->flush();

            $this->addFlash('success', 'Terrain modifié avec succès');
            return $this->redirectToRoute('app_admin_fields');
        }

        return $this->render('admin/fields/edit.html.twig', [
            'field' => $field,
            'competitions' => $competitionRepository->findAll(),
        ]);
    }

    #[Route('/fields/{id}/delete', name: 'app_admin_fields_delete', methods: ['POST'])]
    public function delete(Request $request, Field $field, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$field->getId(), $request->request->get('_token'))) {
            $entityManager->remove($field);
            $entityManager->flush();
            $this->addFlash('success', 'Terrain supprimé avec succès');
        }

        return $this->redirectToRoute('app_admin_fields');
    }
}