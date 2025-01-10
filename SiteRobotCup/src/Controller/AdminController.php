<?php

namespace App\Controller;

use App\Entity\TUserUsr;
use App\Repository\TUserUsrRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[IsGranted('ROLE_ADMIN')]  // Changement ici : on ajoute ROLE_ au début
#[Route('/admin')]
class AdminController extends AbstractController
{
    private const AVAILABLE_TYPES = ['USER', 'ORGA', 'ADMIN'];

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    #[Route('/users', name: 'app_admin_users')]
    public function index(TUserUsrRepository $userRepository): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $userRepository->findAll(),
            'available_types' => self::AVAILABLE_TYPES
        ]);
    }

    #[Route('/user/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TUserUsr $user, EntityManagerInterface $entityManager): Response
    {
        try {
            // Mise à jour du type
            $newType = $request->request->get('type');
            if (in_array($newType, self::AVAILABLE_TYPES)) {
                $user->setType($newType);
            }

            // Mise à jour de l'email
            $newEmail = $request->request->get('email');
            if ($newEmail) {
                $user->setEmail($newEmail);
            }

            // Mise à jour du mot de passe si fourni
            $newPassword = $request->request->get('password');
            if ($newPassword && !empty(trim($newPassword))) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            $entityManager->persist($user);
            $entityManager->flush();
            
            $this->addFlash('success', 'Utilisateur modifié avec succès');
            
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la modification : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/user/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, TUserUsr $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            // Prevent self-deletion
            if ($user !== $this->getUser()) {
                $entityManager->remove($user);
                $entityManager->flush();
                $this->addFlash('success', 'Utilisateur supprimé avec succès');
            } else {
                $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte');
            }
        }

        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/users/search', name: 'app_admin_users_search', methods: ['GET'])]
    public function search(Request $request, TUserUsrRepository $userRepository): Response
    {
        $query = $request->query->get('q', '');
        $users = $userRepository->createQueryBuilder('u')
            ->where('u.email LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->getQuery()
            ->getResult();

        return $this->json([
            'users' => array_map(function($user) {
                return [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'type' => $user->getType(),
                ];
            }, $users)
        ]);
    }
}
