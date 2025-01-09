<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\TTeamTem;
use App\Entity\TMemberMbr;
use App\Entity\TUserUsr;
use App\Form\MemberType;
use App\Form\TeamType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TeamController extends AbstractController
{

    #[Route('/team/{userId}/add-member/{teamId}', name: 'app_team_add_member')]
    public function addMember(
        int $userId,
        int $teamId,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $entityManager->getRepository(TUserUsr::class)->find($userId);
        $team = $entityManager->getRepository(TTeamTem::class)->find($teamId);

        // Vérifier que l'utilisateur existe
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        // Vérifier que l'équipe appartient bien à l'utilisateur
        if (!$team || $team->getUser() !== $user) {
            throw $this->createAccessDeniedException('Cette équipe ne vous appartient pas.');
        }

        // Créer un nouveau membre
        $member = new TMemberMbr();
        
        // Créer le formulaire
        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);

        if (!$team) {
            $this->addFlash('danger', 'Vous devez avoir une equipe pour ajouter des membres');
            return $this->redirectToRoute('app_team_show', ['id' => $user->getId()]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si un membre avec la même adresse e-mail existe déjà dans cette équipe
            $existingMember = $entityManager->getRepository(TMemberMbr::class)
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
                return $this->redirectToRoute('app_team_add_member', ['userId' => $user->getId(), 'teamId' => $teamId]);
            }
        }

        return $this->render('team/add.html.twig', [
            'form' => $form->createView(),
            'team' => $team,
            'members' => $team->getMembers()
        ]);
    }

    #[Route('/team/{id}/{teamId?}', name: 'app_team_show')]
    public function show(
        TUserUsr $user,
        Request $request,
        EntityManagerInterface $entityManager,
        int $teamId = null
    ): Response {
        // Créer une nouveau équipe
        $team = new TTeamTem();

        // Créer le formulaire
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si une équipe avec le même nom existe déjà
            $existingTeam = $entityManager->getRepository(TTeamTem::class)
                ->findOneBy(['name' => $team->getName(), 'user' => $user]);

            if ($existingTeam) {
                $this->addFlash('danger', 'Une équipe avec ce nom existe déjà.');
            } else {
                $team->setUser($user);
                $entityManager->persist($team);
                $entityManager->flush();

                $this->addFlash('success', 'Équipe créée avec succès !');
                return $this->redirectToRoute('app_team_show', ['id' => $user->getId()]);
            }
        }

        $teams = $user->getTeams();

        // Si un teamId est fourni, récupérer les membres associés
        $teamToShow = $teamId ? $entityManager->getRepository(TTeamTem::class)->find($teamId) : null;

        return $this->render('team/show.html.twig', [
            'form' => $form->createView(),
            'team' => $team,
            'teams' => $teams,
            'teamToShow' => $teamToShow, // Équipe dont les membres doivent être affichés
            'user' => $user
        ]);
    }

}