<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\TeamType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/t/team/tem')]
final class TeamCrudController extends AbstractController
{
    #[Route(name: 'app_t_team_tem_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $Teams = $entityManager
            ->getRepository(Team::class)
            ->findAll();

        return $this->render('t_team_tem/index.html.twig', [
            't_team_tems' => $Teams,
        ]);
    }

    #[Route('/new', name: 'app_t_team_tem_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $Team = new Team();
        $form = $this->createForm(TeamType::class, $Team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($Team);
            $entityManager->flush();

            return $this->redirectToRoute('app_t_team_tem_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('t_team_tem/new.html.twig', [
            't_team_tem' => $Team,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_t_team_tem_show', methods: ['GET'])]
    public function show(Team $Team): Response
    {
        return $this->render('t_team_tem/show.html.twig', [
            't_team_tem' => $Team,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_t_team_tem_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Team $Team, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TeamType::class, $Team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_t_team_tem_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('t_team_tem/edit.html.twig', [
            't_team_tem' => $Team,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_t_team_tem_delete', methods: ['POST'])]
    public function delete(Request $request, Team $Team, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$Team->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($Team);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_t_team_tem_index', [], Response::HTTP_SEE_OTHER);
    }
}
