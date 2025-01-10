<?php

namespace App\Controller;

use App\Entity\TCompetitionCmp;
use App\Form\TCompetitionCmpType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/competition')]
class TCompetitionCmpController extends AbstractController
{
    #[Route('/', name: 'app_competition_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $competitions = $entityManager->getRepository(TCompetitionCmp::class)->findAll();
        return $this->render('competition/index.html.twig', [
            'competitions' => $competitions,
        ]);
    }

    #[Route('/new', name: 'app_competition_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $competition = new TCompetitionCmp();
        $form = $this->createForm(TCompetitionCmpType::class, $competition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($competition);
            $entityManager->flush();

            return $this->redirectToRoute('app_competition_index');
        }

        return $this->render('competition/new.html.twig', [
            'competition' => $competition,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_competition_show', methods: ['GET'])]
    public function show(TCompetitionCmp $competition): Response
    {
        return $this->render('competition/show.html.twig', [
            'competition' => $competition,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_competition_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TCompetitionCmp $competition, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TCompetitionCmpType::class, $competition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_competition_index');
        }

        return $this->render('competition/edit.html.twig', [
            'competition' => $competition,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_competition_delete', methods: ['POST'])]
    public function delete(Request $request, TCompetitionCmp $competition, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$competition->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($competition);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_competition_index');
    }
}
