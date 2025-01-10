<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\TTeamTem;
use App\Entity\TMemberMbr;
use App\Entity\TUserUsr;
use App\Form\MemberType;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Form\TeamType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\TChampionshipChp;
use App\Repository\TeamRepository;
use App\Repository\ChampionshipRepository;

class TeamController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/team/{userId}/add-member/{teamId}', name: 'app_team_add_member')]
    public function addMember(
        string $userId,
        string $teamId,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $entityManager->getRepository(TUserUsr::class)->find((int)$userId);
        $team = $entityManager->getRepository(TTeamTem::class)->find((int)$teamId);

        // Vérifier les permissions
        if (!$this->isGranted('ROLE_ADMIN') && $team->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas les droits pour modifier cette équipe');
        }

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
        Request $request,
        EntityManagerInterface $entityManager,
        ?string $teamId = null
    ): Response {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Créer une nouvelle équipe
        $team = new TTeamTem();
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingTeam = $entityManager->getRepository(TTeamTem::class)
                ->findOneBy(['name' => $team->getName(), 'user' => $user]);

            if ($existingTeam) {
                $this->addFlash('danger', 'Une équipe avec ce nom existe déjà.');
            } else {
                $team->setUser($user);
                $entityManager->persist($team);
                $entityManager->flush();

                $this->addFlash('success', 'Équipe créée avec succès !');
                return $this->redirectToRoute('app_team_show');
            }
        }

        // Récupérer les équipes de l'utilisateur connecté
        $teams = $user->getTeams();

        // Si un teamId est fourni, récupérer les membres associés
        $teamToShow = $teamId ? $entityManager->getRepository(TTeamTem::class)->find((int)$teamId) : null;
        
        if ($teamToShow && !$this->isGranted('ROLE_ADMIN') && $teamToShow->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette équipe');
        }

        $championships = $entityManager->getRepository(TChampionshipChp::class)->findAll();

        return $this->render('team/show.html.twig', [
            'form' => $form->createView(),
            'team' => $team,
            'teams' => $teams,
            'teamToShow' => $teamToShow,
            'user' => $user,
            'championships' => $championships
        ]);
    }

    #[Route('/team/register-championship/{teamId}', name: 'app_team_register_championship', methods: ['GET', 'POST'])]
    public function registerChampionship(
        Request $request,
        string $teamId,
        EntityManagerInterface $entityManager
    ): Response {
        $team = $entityManager->getRepository(TTeamTem::class)->find((int)$teamId);
        if (!$team) {
            throw $this->createNotFoundException('Équipe non trouvée');
        }

        // Vérifier les permissions
        if (!$this->isGranted('ROLE_ADMIN') && $team->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas les droits pour inscrire cette équipe');
        }

        $championships = $entityManager->getRepository(TChampionshipChp::class)->findAll();

        if ($request->isMethod('POST')) {
            $championshipId = $request->request->get('championship');
            $championship = $entityManager->getRepository(TChampionshipChp::class)->find($championshipId);
            
            if ($championship) {
                try {
                    $team->addChampionship($championship);
                    $entityManager->persist($team);
                    $entityManager->flush();
                    
                    $this->addFlash('success', 'Équipe inscrite au championnat avec succès');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'inscription');
                }
                return $this->redirectToRoute('app_team_show');
            }
        }

        return $this->render('team/register_championship.html.twig', [
            'team' => $team,
            'championships' => $championships,
        ]);
    }

}