<?php

namespace App\Controller;

use App\Entity\TTeamTem;
use App\Form\TTeamTemType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/t/team/tem')]
final class TTeamTemController extends AbstractController
{
    #[Route(name: 'app_t_team_tem_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $tTeamTems = $entityManager
            ->getRepository(TTeamTem::class)
            ->findAll();

        return $this->render('t_team_tem/index.html.twig', [
            't_team_tems' => $tTeamTems,
        ]);
    }

    #[Route('/new', name: 'app_t_team_tem_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tTeamTem = new TTeamTem();
        $form = $this->createForm(TTeamTemType::class, $tTeamTem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tTeamTem);
            $entityManager->flush();

            return $this->redirectToRoute('app_t_team_tem_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('t_team_tem/new.html.twig', [
            't_team_tem' => $tTeamTem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_t_team_tem_show', methods: ['GET'])]
    public function show(TTeamTem $tTeamTem): Response
    {
        return $this->render('t_team_tem/show.html.twig', [
            't_team_tem' => $tTeamTem,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_t_team_tem_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TTeamTem $tTeamTem, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TTeamTemType::class, $tTeamTem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_t_team_tem_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('t_team_tem/edit.html.twig', [
            't_team_tem' => $tTeamTem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_t_team_tem_delete', methods: ['POST'])]
    public function delete(Request $request, TTeamTem $tTeamTem, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tTeamTem->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tTeamTem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_t_team_tem_index', [], Response::HTTP_SEE_OTHER);
    }
}
