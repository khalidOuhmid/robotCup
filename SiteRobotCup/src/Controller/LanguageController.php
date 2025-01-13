<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class LanguageController
{
    #[Route('/set-language/{locale}', name: 'app_set_language', methods: ['GET'])]
    public function setLanguage(string $locale, Request $request): Response
    {
        // Redirige vers la page précédente
        $referer = $request->headers->get('referer', '/'); // Retourne à la page d'accueil si aucun referer n'est défini
        $response = new RedirectResponse($referer);

        // Définit un cookie pour sauvegarder la langue
        $response->headers->setCookie(new \Symfony\Component\HttpFoundation\Cookie('locale', $locale, strtotime('+1 year')));

        return $response;
    }
}
