<?php

namespace App\Controller;

use App\Entity\TChampionshipChp;
use App\Form\TChampionshipChpType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/t/championship/chp')]
final class TChampionshipChpController extends AbstractController
{
    #[Route(name: 'app_t_championship_chp_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $tChampionshipChps = $entityManager
            ->getRepository(TChampionshipChp::class)
            ->findAll();

        return $this->render('t_championship_chp/index.html.twig', [
            't_championship_chps' => $tChampionshipChps,
        ]);
    }

    #[Route('/new', name: 'app_t_championship_chp_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tChampionshipChp = new TChampionshipChp();
        $form = $this->createForm(TChampionshipChpType::class, $tChampionshipChp);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tChampionshipChp);
            $entityManager->flush();

            return $this->redirectToRoute('app_t_championship_chp_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('t_championship_chp/new.html.twig', [
            't_championship_chp' => $tChampionshipChp,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_t_championship_chp_show', methods: ['GET'])]
    public function show(TChampionshipChp $tChampionshipChp): Response
    {
        return $this->render('t_championship_chp/show.html.twig', [
            't_championship_chp' => $tChampionshipChp,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_t_championship_chp_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TChampionshipChp $tChampionshipChp, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TChampionshipChpType::class, $tChampionshipChp);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_t_championship_chp_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('t_championship_chp/edit.html.twig', [
            't_championship_chp' => $tChampionshipChp,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_t_championship_chp_delete', methods: ['POST'])]
    public function delete(Request $request, TChampionshipChp $tChampionshipChp, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tChampionshipChp->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tChampionshipChp);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_t_championship_chp_index', [], Response::HTTP_SEE_OTHER);
    }
}
