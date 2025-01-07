<?php

namespace App\Controller;

use App\Entity\TMemberMbr;
use App\Form\TMemberMbrType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/t/member/mbr')]
final class TMemberMbrController extends AbstractController
{
    #[Route(name: 'app_t_member_mbr_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $tMemberMbrs = $entityManager
            ->getRepository(TMemberMbr::class)
            ->findAll();

        return $this->render('t_member_mbr/index.html.twig', [
            't_member_mbrs' => $tMemberMbrs,
        ]);
    }

    #[Route('/new', name: 'app_t_member_mbr_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tMemberMbr = new TMemberMbr();
        $form = $this->createForm(TMemberMbrType::class, $tMemberMbr);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tMemberMbr);
            $entityManager->flush();

            return $this->redirectToRoute('app_t_member_mbr_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('t_member_mbr/new.html.twig', [
            't_member_mbr' => $tMemberMbr,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_t_member_mbr_show', methods: ['GET'])]
    public function show(TMemberMbr $tMemberMbr): Response
    {
        return $this->render('t_member_mbr/show.html.twig', [
            't_member_mbr' => $tMemberMbr,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_t_member_mbr_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TMemberMbr $tMemberMbr, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TMemberMbrType::class, $tMemberMbr);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_t_member_mbr_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('t_member_mbr/edit.html.twig', [
            't_member_mbr' => $tMemberMbr,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_t_member_mbr_delete', methods: ['POST'])]
    public function delete(Request $request, TMemberMbr $tMemberMbr, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tMemberMbr->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tMemberMbr);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_t_member_mbr_index', [], Response::HTTP_SEE_OTHER);
    }
}
