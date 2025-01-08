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

        // Récupération de l'utilisateur qui à créé l'équipe
        $user = $team->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            // Associer le membre à l'équipe
            $member->setTeam($team);
            $team->addMember($member);
            
            // Sauvegarder en base de données
            $entityManager->persist($member);
            $entityManager->flush();

            $this->addFlash('success', 'Membre ajouté avec succès !');
            return $this->redirectToRoute('app_team_show', ['id' => $user->getId()]);
        }

        return $this->render('team/add.html.twig', [
            'form' => $form->createView(),
            'team' => $team,
            'members' => $team->getMembers()
        ]);
    }

    #[Route('/team/{id}', name: 'app_team_show')]
    public function show(
        TUserUsr $user,
        Request $request,
        EntityManagerInterface $entityManager
        ): Response
    {
        // Créer une nouveau équipe
        $team = new TTeamTem();

        // Créer le formulaire
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Associer le membre à l'équipe
            $team->setUser($user);
            
            // Sauvegarder en base de données
            $entityManager->persist($team);
            $entityManager->flush();

            $this->addFlash('success', 'Membre ajouté avec succès !');
            return $this->redirectToRoute('app_team_show', ['id' => $user->getId()]);
        }

        $teams = $user->getTeams();

        return $this->render('team/show.html.twig', [
            'form' => $form->createView(),
            'team' => $team,
            'teams' => $teams

        ]);
    }

}
