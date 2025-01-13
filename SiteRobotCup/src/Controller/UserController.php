<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Controller for managing users.
 */
#[Route('/t/user/usr')]
final class UserController extends AbstractController
{
    /**
     * @var UserPasswordHasherInterface Password hasher service.
     */
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * Constructor.
     *
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Displays a list of users.
     *
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route(name: 'app_t_user_usr_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('t_user_usr/index.html.twig', [
            't_user_usrs' => $users,
        ]);
    }

    /**
     * Creates a new user.
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/new', name: 'app_t_user_usr_new', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password before saving
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            // Add success flash message and redirect
            $this->addFlash('success', 'Utilisateur créé avec succès');
            return $this->redirectToRoute('app_t_user_usr_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('t_user_usr/new.html.twig', [
            't_user_usr' => $user,
            'form' => $form,
        ]);
    }

    /**
     * Displays details of a specific user.
     *
     * @param User $user
     * @return Response
     */
    #[Route('/{id}', name: 'app_t_user_usr_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('t_user_usr/show.html.twig', [
            't_user_usr' => $user,
        ]);
    }

    /**
     * Edits an existing user.
     *
     * @param Request $request
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_t_user_usr_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password only if it has been modified
            if ($form->get('password')->getData()) {
                $hashedPassword = $this->passwordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                );
                $user->setPassword($hashedPassword);
            }

            // Save changes to the database
            $entityManager->flush();

            return $this->redirectToRoute('app_t_user_usr_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('t_user_usr/edit.html.twig', [
            't_user_usr' => $user,
            'form' => $form,
        ]);
    }

    /**
     * Deletes a user.
     *
     * @param Request $request
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}', name: 'app_t_user_usr_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . (string)$user->getId(), (string)$request->request->get('_token'))) {
            // Remove the user from the database
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_t_user_usr_index', [], Response::HTTP_SEE_OTHER);
    }
}
