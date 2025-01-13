<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Controller handling user authentication.
 */
class LoginController extends AbstractController
{
    /**
     * Handles the login process.
     *
     * @param AuthenticationUtils $authenticationUtils
     * @param Request $request
     * @return Response
     */
    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(
        AuthenticationUtils $authenticationUtils,
        Request $request
    ): Response {
        if ($this->isUserLoggedIn()) {
            return $this->redirectToRoute('app_page_tableau_scores');
        }

        return $this->renderLoginForm(
            $authenticationUtils->getLastUsername(),
            $authenticationUtils->getLastAuthenticationError()
        );
    }

    /**
     * Handles the logout process.
     * This method is intentionally empty as it will be intercepted by the firewall.
     */
    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        // Method intentionally left empty
        // It will be intercepted by the logout key on the firewall
        throw new \LogicException('This method should not be reached.');
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
     * Renders the login form with the provided parameters.
     *
     * @param string|null $lastUsername
     * @param mixed $error
     * @return Response
     */
    private function renderLoginForm(?string $lastUsername, $error): Response
    {
        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
}
