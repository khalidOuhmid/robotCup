<?php

namespace App\Controller;

use App\Entity\Championship;
use App\Entity\Competition;
use App\Entity\Encounter;
use App\Entity\Member;
use App\Entity\Team;
use App\Entity\User;
use App\Form\MemberForm;
use App\Form\TeamChampionshipForm;
use App\Form\TeamType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller managing team-related operations.
 */
class TeamController extends AbstractController
{
    /**
     * Liste toutes les équipes (admin uniquement)
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/teams', name: 'app_admin_teams')]
    public function adminTeams(EntityManagerInterface $entityManager): Response
    {
        $teams = $entityManager->getRepository(Team::class)->findAll();
        return $this->render('admin/teams/index.html.twig', [
            'teams' => $teams
        ]);
    }

    /**
     * Supprime une équipe (admin uniquement)
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/team/{id}/delete', name: 'app_admin_team_delete')]
    public function deleteTeam(Team $team, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($team);
        $entityManager->flush();
        
        $this->addFlash('success', 'Équipe supprimée avec succès');
        return $this->redirectToRoute('app_admin_teams');
    }

    /**
     * Modifie une équipe (admin uniquement)
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/team/{id}/edit', name: 'app_admin_team_edit')]
    public function editTeam(Team $team, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Équipe modifiée avec succès');
            return $this->redirectToRoute('app_admin_teams');
        }

        return $this->render('admin/teams/edit.html.twig', [
            'form' => $form->createView(),
            'team' => $team
        ]);
    }

    /**
     * Adds a new member to a team.
     *
     * @param string $userId The user identifier
     * @param string $teamId The team identifier
     * @param Request $request The HTTP request
     * @param EntityManagerInterface $entityManager The entity manager
     * @return Response
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/team/{userId}/add-member/{teamId}', name: 'app_team_add_member')]
    public function addMember(
        string $userId,
        string $teamId,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $entityManager->getRepository(User::class)->find((int)$userId);
        $team = $entityManager->getRepository(Team::class)->find((int)$teamId);

        if (!$this->isGranted('ROLE_ADMIN') && $team->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas les droits pour modifier cette équipe');
        }

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        if (!$team) {
            throw $this->createNotFoundException('Équipe non trouvée.');
        }
        
        if (!$this->isGranted('ROLE_ADMIN') && $team->getUser() !== $user) {
            throw $this->createAccessDeniedException('Cette équipe ne vous appartient pas.');
        }

        $member = new Member();
        $form = $this->createForm(MemberForm::class, $member);
        $form->handleRequest($request);

        if (!$team) {
            $this->addFlash('danger', 'Vous devez avoir une équipe pour ajouter des membres');
            return $this->redirectToRoute('app_team_show', ['id' => $team->getId()]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $existingMember = $entityManager->getRepository(Member::class)
                ->findOneBy(['email' => $member->getEmail(), 'team' => $team]);

            if ($existingMember) {
                $this->addFlash('danger', 'Un membre avec cette adresse e-mail existe déjà dans l\'équipe.');
            } else {
                $member->setTeam($team);
                $team->addMember($member);

                $entityManager->persist($member);
                $entityManager->flush();
                $entityManager->refresh($team);

                $this->addFlash('success', 'Membre ajouté avec succès !');
                return $this->redirectToRoute('app_team_add_member', [
                    'userId' => $user->getId(),
                    'teamId' => $teamId
                ]);
            }
        }

        return $this->render('team/add.html.twig', [
            'form' => $form->createView(),
            'team' => $team,
            'members' => $team->getMembers()
        ]);
    }

    /**
     * Affiche et gère la création d'équipe (modifié pour la limite d'une équipe par utilisateur par compétition)
     */
    #[Route('/team/new', name: 'app_team_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->beginTransaction();
                
                // On s'assure qu'une compétition est sélectionnée
                if (!$team->getCompetition()) {
                    $this->addFlash('error', 'Vous devez sélectionner une compétition');
                    return $this->render('admin/teams/index.html.twig', ['form' => $form->createView()]);
                }

                // Vérifie si l'utilisateur a déjà une équipe pour cette compétition
                $existingTeam = $entityManager->getRepository(Team::class)
                    ->findOneBy(['user' => $user, 'competition' => $team->getCompetition()]);

                if ($existingTeam) {
                    $this->addFlash('error', 'Vous avez déjà une équipe pour cette compétition');
                    return $this->redirectToRoute('app_team_show', ['id' => $user->getId()]);
                }

                $team->setUser($this->getUser());
                $entityManager->persist($team);
                $entityManager->flush();

                $entityManager->commit();
                $this->addFlash('success', 'Équipe créée avec succès');
                return $this->redirectToRoute('app_team_show', ['id' => $user->getId()]);
            } catch (\Exception $e) {
                $entityManager->rollback();
                $this->addFlash('error', 'Une erreur est survenue lors de la création de l\'équipe');
                return $this->redirectToRoute('app_team_show', ['id' => $user->getId()]);
            }
        }

        return $this->render('admin/teams/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche les équipes avec des droits différents selon le rôle
     */
    #[Route('/team/{teamId?}', name: 'app_team_show')]
    public function show(
        Request $request,
        EntityManagerInterface $entityManager,
        ?string $teamId = null
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Gestion des équipes affichées
        $teams = $this->isGranted('ROLE_ADMIN') ? 
            $entityManager->getRepository(Team::class)->findAll() : 
            $user->getTeams();

        $teamToShow = $teamId ? $entityManager->getRepository(Team::class)->find((int) $teamId) : null;

        // Vérifier l'accès à l'équipe
        if ($teamToShow && !$this->isGranted('ROLE_ADMIN') && $teamToShow->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette équipe.');
        }

        // Formulaire de création d'équipe
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingTeam = $entityManager->getRepository(Team::class)
                ->findOneBy(['user' => $user, 'competition' => $team->getCompetition()]);

            if ($existingTeam) {
                $this->addFlash('danger', sprintf(
                    'Vous avez déjà une équipe pour la compétition "%s".',
                    $team->getCompetition()->getCmpName()
                ));
            } else {
                $team->setUser($user);
                $entityManager->persist($team);
                $entityManager->flush();

                $this->addFlash('success', 'Équipe créée avec succès !');
                return $this->redirectToRoute('app_team_show', ['teamId' => $team->getId()]);
            }
        }

        return $this->render('team/show.html.twig', [
            'form' => $form->createView(),
            'team' => $team,
            'teams' => $teams,
            'teamToShow' => $teamToShow,
            'user' => $user,
            'isAdmin' => $this->isGranted('ROLE_ADMIN')
        ]);
    }

    /**
     * Registers a team for a championship.
     *
     * @param Request $request The HTTP request
     * @param EntityManagerInterface $entityManager The entity manager
     * @param Team $team The team to register
     * @return Response
     */
    #[Route('/team/register-championship/{teamId}', name: 'app_team_register_championship', methods: ['GET', 'POST'])]
    public function registerChampionship(
        Request $request,
        EntityManagerInterface $entityManager,
        Team $team
    ): Response {
        $encounter = new Encounter();
        $encounter->setTeamBlue($team);
        
        $form = $this->createForm(TeamChampionshipForm::class, $encounter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encounter->setState('PROGRAMMEE');
            $encounter->setDateBegin(new \DateTime());
            $encounter->setDateEnd(new \DateTime('+1 hour'));
            $entityManager->persist($encounter);
            $entityManager->flush();

            $this->addFlash('success', 'Équipe inscrite au championnat avec succès');
            return $this->redirectToRoute('app_team_show', ['id' => $team->getId()]);
        }

        return $this->render('team/register_championship.html.twig', [
            'team' => $team,
            'form' => $form
        ]);
    }
}
