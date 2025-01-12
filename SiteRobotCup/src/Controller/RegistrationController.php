<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller handling user registration.
 */
class RegistrationController extends AbstractController
{
    private const DEFAULT_USER_TYPE = 'USER';

    /**
     * Handles user registration process.
     *
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param Security $security
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/register', name: 'register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isUserLoggedIn()) {
            return $this->redirectToRoute('app_page_tableau_scores');
        }

        $user = new User();
        $form = $this->createRegistrationForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->processUserRegistration(
                $user,
                $form,
                $userPasswordHasher,
                $entityManager
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    /**
     * Checks if a user is currently logged in.
     *
     * @return bool
     */
    private function isUserLoggedIn(): bool
    {
        return $this->getUser() !== null;
    }

    /**
     * Creates the registration form.
     *
     * @param User $user
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createRegistrationForm(User $user): \Symfony\Component\Form\FormInterface
    {
        return $this->createForm(RegistrationForm::class, $user);
    }

    /**
     * Processes the user registration.
     *
     * @param User $user
     * @param \Symfony\Component\Form\FormInterface $form
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EntityManagerInterface $entityManager
     * @return void
     */
    private function processUserRegistration(
        User $user,
        \Symfony\Component\Form\FormInterface $form,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): void {
        /** @var string $plainPassword */
        $plainPassword = $form->get('plainPassword')->getData();

        $user->setType(self::DEFAULT_USER_TYPE);
        $user->setPassword(
            $passwordHasher->hashPassword($user, $plainPassword)
        );

        $entityManager->persist($user);
        $entityManager->flush();
    }
}
