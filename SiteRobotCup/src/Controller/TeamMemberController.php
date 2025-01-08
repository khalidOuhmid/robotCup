<?php

namespace App\Controller;

use App\Entity\TMemberMbr;
use App\Entity\TTeamTem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\MemberType;

class TeamMemberController extends AbstractController
{
    #[Route('/team/{id}/add-member', name: 'app_team_add_member')]
    public function addMember(
        Request $request,
        EntityManagerInterface $entityManager,
        TTeamTem $team
    ): Response {
        // Créer un nouveau membre
        $member = new TMemberMbr();
        
        // Créer le formulaire
        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Associer le membre à l'équipe
            $member->setTeam($team);
            
            // Sauvegarder en base de données
            $entityManager->persist($member);
            $entityManager->flush();

            $this->addFlash('success', 'Membre ajouté avec succès !');
            return $this->redirectToRoute('app_team_show', ['id' => $team->getId()]);
        }

        return $this->render('team_member/add.html.twig', [
            'form' => $form->createView(),
            'team' => $team
        ]);
    }

    #[Route('/team/{id}', name: 'app_team_show')]
public function show(TTeamTem $team): Response
{
    return $this->render('team_member/show.html.twig', [
        'team' => $team,
        'members' => $team->getMembers()
    ]);
}

}
