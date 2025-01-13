<?php

namespace App\Controller;

use App\Entity\Competition;
use App\Form\CompetitionForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller for managing competitions.
 */
#[Route('/competition')]
class CompetitionController extends AbstractController
{
    /**
     * Lists all competitions.
     *
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/', name: 'app_competition_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $competitions = $entityManager
            ->getRepository(Competition::class)
            ->findAll();

        return $this->render('competition/index.html.twig', [
            'competitions' => $competitions,
        ]);
    }

    /**
     * Creates a new competition.
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/new', name: 'app_competition_new', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $competition = new Competition();
        $form = $this->createCompetitionForm($competition);
        $form->handleRequest($request);

        if ($this->isValidFormSubmission($form)) {
            $this->saveCompetition($competition, $entityManager);
            return $this->redirectToRoute('app_competition_index');
        }

        return $this->renderCompetitionForm('competition/new.html.twig', $competition, $form);
    }

    /**
     * Displays a competition.
     *
     * @param Competition $competition
     * @return Response
     */
    #[Route('/{id}', name: 'app_competition_show', methods: ['GET'])]
    public function show(Competition $competition): Response
    {
        return $this->render('competition/show.html.twig', [
            'competition' => $competition,
        ]);
    }

    /**
     * Edits a competition.
     *
     * @param Request $request
     * @param Competition $competition
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_competition_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Competition $competition,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createCompetitionForm($competition);
        $form->handleRequest($request);

        if ($this->isValidFormSubmission($form)) {
            $entityManager->flush();
            return $this->redirectToRoute('app_competition_index');
        }

        return $this->renderCompetitionForm('competition/edit.html.twig', $competition, $form);
    }

    /**
     * Deletes a competition.
     *
     * @param Request $request
     * @param Competition $competition
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}', name: 'app_competition_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Competition $competition,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isValidCsrfToken($request, $competition)) {
            $this->removeCompetition($competition, $entityManager);
        }

        return $this->redirectToRoute('app_competition_index');
    }

    /**
     * Creates a competition form.
     *
     * @param Competition $competition
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createCompetitionForm(Competition $competition): \Symfony\Component\Form\FormInterface
    {
        return $this->createForm(CompetitionForm::class, $competition);
    }

    /**
     * Checks if form submission is valid.
     *
     * @param \Symfony\Component\Form\FormInterface $form
     * @return bool
     */
    private function isValidFormSubmission(\Symfony\Component\Form\FormInterface $form): bool
    {
        return $form->isSubmitted() && $form->isValid();
    }

    /**
     * Renders the competition form.
     *
     * @param string $template
     * @param Competition $competition
     * @param \Symfony\Component\Form\FormInterface $form
     * @return Response
     */
    private function renderCompetitionForm(
        string $template,
        Competition $competition,
        \Symfony\Component\Form\FormInterface $form
    ): Response {
        return $this->render($template, [
            'competition' => $competition,
            'form' => $form,
        ]);
    }

    /**
     * Validates CSRF token.
     *
     * @param Request $request
     * @param Competition $competition
     * @return bool
     */
    private function isValidCsrfToken(Request $request, Competition $competition): bool
    {
        return $this->isCsrfTokenValid(
            'delete' . $competition->getId(),
            $request->getPayload()->getString('_token')
        );
    }

    /**
     * Saves a competition to the database.
     *
     * @param Competition $competition
     * @param EntityManagerInterface $entityManager
     */
    private function saveCompetition(
        Competition $competition,
        EntityManagerInterface $entityManager
    ): void {
        $entityManager->persist($competition);
        $entityManager->flush();
    }

    /**
     * Removes a competition from the database.
     *
     * @param Competition $competition
     * @param EntityManagerInterface $entityManager
     */
    private function removeCompetition(
        Competition $competition,
        EntityManagerInterface $entityManager
    ): void {
        $entityManager->remove($competition);
        $entityManager->flush();
    }
}
