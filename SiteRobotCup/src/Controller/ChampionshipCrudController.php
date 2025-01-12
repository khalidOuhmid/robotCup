<?php

namespace App\Controller;

use App\Entity\Championship;
use App\Form\ChampionshipForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/t/championship/chp')]
final class ChampionshipCrudController extends AbstractController
{
    #[Route(name: 'app_t_championship_chp_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $Championships = $entityManager
            ->getRepository(Championship::class)
            ->findAll();

        return $this->render('t_championship_chp/index.html.twig', [
            't_championship_chps' => $Championships,
        ]);
    }

    #[Route('/new', name: 'app_t_championship_chp_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $Championship = new Championship();
        $form = $this->createForm(ChampionshipForm::class, $Championship);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($Championship);
            $entityManager->flush();

            return $this->redirectToRoute('app_t_championship_chp_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('t_championship_chp/new.html.twig', [
            't_championship_chp' => $Championship,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_t_championship_chp_show', methods: ['GET'])]
    public function show(Championship $Championship): Response
    {
        return $this->render('t_championship_chp/show.html.twig', [
            't_championship_chp' => $Championship,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_t_championship_chp_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Championship $Championship, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChampionshipForm::class, $Championship);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_t_championship_chp_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('t_championship_chp/edit.html.twig', [
            't_championship_chp' => $Championship,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_t_championship_chp_delete', methods: ['POST'])]
    public function delete(Request $request, Championship $Championship, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$Championship->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($Championship);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_t_championship_chp_index', [], Response::HTTP_SEE_OTHER);
    }
}
