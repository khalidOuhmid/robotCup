<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Competition;
use App\Entity\Tournament;
use App\Entity\Championship;
use App\Repository\UserRepository;
use App\Form\CompetitionType;
use App\Service\TournamentGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller for admin operations.
 */
#[IsGranted('ROLE_ADMIN')]
#[Route('/admin')]
class AdminController extends AbstractController
{
    private const AVAILABLE_TYPES = ['USER', 'ORGA', 'ADMIN'];

    /**
     * @var UserPasswordHasherInterface
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
     * Displays all users.
     *
     * @param UserRepository $userRepository
     * @return Response
     */
    #[Route('/users', name: 'app_admin_users', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $userRepository->findAll(),
            'available_types' => self::AVAILABLE_TYPES
        ]);
    }

    /**
     * Edits a user.
     *
     * @param Request $request
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/user/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response {
        try {
            $this->updateUserData($request, $user);
            $this->saveUser($user, $entityManager);
            $this->addFlash('success', 'Utilisateur modifié avec succès');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la modification : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_admin_users');
    }

    /**
     * Deletes a user.
     *
     * @param Request $request
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/user/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->canDeleteUser($request, $user)) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur supprimé avec succès');
        }

        return $this->redirectToRoute('app_admin_users');
    }

    /**
     * Searches for users.
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    #[Route('/users/search', name: 'app_admin_users_search', methods: ['GET'])]
    public function search(
        Request $request,
        UserRepository $userRepository
    ): JsonResponse {
        $query = $request->query->get('q', '');
        $users = $this->searchUsers($query, $userRepository);

        return $this->json([
            'users' => $this->formatUsersForJson($users)
        ]);
    }

    /**
     * Updates user data from request.
     *
     * @param Request $request
     * @param User $user
     */
    private function updateUserData(Request $request, User $user): void
    {
        $newType = $request->request->get('type');
        if (in_array($newType, self::AVAILABLE_TYPES)) {
            $user->setType($newType);
        }

        $newEmail = $request->request->get('email');
        if ($newEmail) {
            $user->setEmail($newEmail);
        }

        $newPassword = $request->request->get('password');
        if ($newPassword && !empty(trim($newPassword))) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);
        }
    }

    /**
     * Saves user to database.
     *
     * @param User $user
     * @param EntityManagerInterface $entityManager
     */
    private function saveUser(User $user, EntityManagerInterface $entityManager): void
    {
        $entityManager->persist($user);
        $entityManager->flush();
    }

    /**
     * Checks if user can be deleted.
     *
     * @param Request $request
     * @param User $user
     * @return bool
     */
    private function canDeleteUser(Request $request, User $user): bool
    {
        if (!$this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            return false;
        }

        if ($user === $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte');
            return false;
        }

        return true;
    }

    /**
     * Searches users by query.
     *
     * @param string $query
     * @param UserRepository $userRepository
     * @return array
     */
    private function searchUsers(string $query, UserRepository $userRepository): array
    {
        return $userRepository->createQueryBuilder('u')
            ->where('u.email LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Formats users for JSON response.
     *
     * @param array $users
     * @return array
     */
    private function formatUsersForJson(array $users): array
    {
        return array_map(function(User $user) {
            return [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'type' => $user->getType(),
            ];
        }, $users);
    }

    #[Route('/admin/competition/new', name: 'app_admin_competition_new')]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager,
        TournamentGenerator $tournamentGenerator
    ): Response {
        $competition = new Competition();
        $form = $this->createForm(CompetitionType::class, $competition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Création du championnat
            $championship = new Championship();
            $championship->setCompetition($competition);
            
            $entityManager->persist($competition);
            $entityManager->persist($championship);

            // Si le tournoi est demandé
            if ($form->get('includeTournament')->getData()) {
                $tournament = new Tournament();
                $tournament->setCompetition($competition);
                $tournament->setIncludeThirdPlace($form->get('includeThirdPlace')->getData());
                
                $entityManager->persist($tournament);
            }

            $entityManager->flush();

            $this->addFlash('success', 'La compétition a été créée avec succès.');
            return $this->redirectToRoute('app_admin_competitions');
        }

        return $this->render('admin/competition/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
