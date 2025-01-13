<?php

namespace App\Controller;

use App\Entity\Member;
use App\Form\MemberForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/t/member/mbr')]
final class MemberController extends AbstractController
{
    #[Route(name: 'app_t_member_mbr_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $Members = $entityManager
            ->getRepository(Member::class)
            ->findAll();

        return $this->render('t_member_mbr/index.html.twig', [
            't_member_mbrs' => $Members,
        ]);
    }

    #[Route('/new', name: 'app_t_member_mbr_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $Member = new Member();
        $form = $this->createForm(MemberForm::class, $Member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($Member);
            $entityManager->flush();

            return $this->redirectToRoute('app_t_member_mbr_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('t_member_mbr/new.html.twig', [
            't_member_mbr' => $Member,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_t_member_mbr_show', methods: ['GET'])]
    public function show(Member $Member): Response
    {
        return $this->render('t_member_mbr/show.html.twig', [
            't_member_mbr' => $Member,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_t_member_mbr_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Member $Member, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MemberForm::class, $Member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_t_member_mbr_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('t_member_mbr/edit.html.twig', [
            't_member_mbr' => $Member,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_t_member_mbr_delete', methods: ['POST'])]
    public function delete(Request $request, Member $Member, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$Member->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($Member);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_t_member_mbr_index', [], Response::HTTP_SEE_OTHER);
    }
}
