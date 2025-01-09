<?php

namespace App\Controller;

use App\Entity\TUserUsr;
use App\Form\TUserUsrType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/t/user/usr')]
final class TUserUsrController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    #[Route(name: 'app_t_user_usr_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $tUserUsrs = $entityManager
            ->getRepository(TUserUsr::class)
            ->findAll();

        return $this->render('t_user_usr/index.html.twig', [
            't_user_usrs' => $tUserUsrs,
        ]);
    }

    #[Route('/new', name: 'app_t_user_usr_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tUserUsr = new TUserUsr();
        $form = $this->createForm(TUserUsrType::class, $tUserUsr);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password before saving
            $hashedPassword = $this->passwordHasher->hashPassword(
                $tUserUsr,
                $tUserUsr->getPassword()
            );
            $tUserUsr->setPassword($hashedPassword);
            
            $entityManager->persist($tUserUsr);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur créé avec succès');
            return $this->redirectToRoute('app_admin_users', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('t_user_usr/new.html.twig', [
            't_user_usr' => $tUserUsr,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_t_user_usr_show', methods: ['GET'])]
    public function show(TUserUsr $tUserUsr): Response
    {
        return $this->render('t_user_usr/show.html.twig', [
            't_user_usr' => $tUserUsr,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_t_user_usr_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TUserUsr $tUserUsr, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TUserUsrType::class, $tUserUsr);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Only hash the password if it has been modified
            if ($form->get('password')->getData()) {
                $hashedPassword = $this->passwordHasher->hashPassword(
                    $tUserUsr,
                    $form->get('password')->getData()
                );
                $tUserUsr->setPassword($hashedPassword);
            }
            
            $entityManager->flush();

            return $this->redirectToRoute('app_t_user_usr_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('t_user_usr/edit.html.twig', [
            't_user_usr' => $tUserUsr,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_t_user_usr_delete', methods: ['POST'])]
    public function delete(Request $request, TUserUsr $tUserUsr, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tUserUsr->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tUserUsr);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_t_user_usr_index', [], Response::HTTP_SEE_OTHER);
    }
}
