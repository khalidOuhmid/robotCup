<?php
// src/Controller/Admin/CompetitionController.php
namespace App\Controller;

use App\Entity\Competition;
use App\Entity\Championship;
use App\Entity\Tournament;
use App\Form\CompetitionType;
use App\Service\TournamentGenerator;
use App\Service\ChampionshipScheduler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class CompetitionController extends AbstractController
{
    #[Route('/admin/competition/new', name: 'app_admin_competition_new')]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager
    ): Response {
        $competition = new Competition();
        $form = $this->createForm(CompetitionType::class, $competition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Vérifier d'abord que la date de début est avant la date de fin
                if ($competition->getCmpDateBegin() >= $competition->getCmpDateEnd()) {
                    throw new \Exception('La date de début doit être antérieure à la date de fin');
                }

                // Vérifier le chevauchement des dates
                $overlappingCompetitions = $entityManager->getRepository(Competition::class)
                    ->findOverlappingCompetitions(
                        $competition->getCmpDateBegin(),
                        $competition->getCmpDateEnd()
                    );

                if (!empty($overlappingCompetitions)) {
                    throw new \Exception('Une autre compétition existe déjà sur cette période');
                }

                $entityManager->beginTransaction();
                
                try {
                    // Création du championnat
                    $championship = new Championship();
                    $championship->setCompetition($competition);
                    
                    $entityManager->persist($competition);
                    $entityManager->persist($championship);

                    // Si le tournoi est demandé
                    if ($form->get('includeTournament')->getData()) {
                        $tournament = new Tournament();
                        $tournament->setCompetition($competition);
                        
                        $includeThirdPlace = $form->get('includeThirdPlace')->getData();
                        $tournament->setIncludeThirdPlace($includeThirdPlace);
                        
                        $entityManager->persist($tournament);
                    }

                    $entityManager->flush();
                    $entityManager->commit();
                    $this->addFlash('success', 'La compétition a été créée avec succès.');
                    return $this->redirectToRoute('app_default');
                    
                } catch (\Exception $e) {
                    $entityManager->rollback();
                    throw $e;
                }
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('admin/competition/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/competition', name: 'app_competition_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $competitions = $entityManager->getRepository(Competition::class)
            ->findBy([], ['cmpDateBegin' => 'DESC']);

        return $this->render('competition/index.html.twig', [
            'competitions' => $competitions,
        ]);
    }

    #[Route('/competition/{id}', name: 'app_competition_show')]
    public function show(Competition $competition): Response
    {
        return $this->render('competition/show.html.twig', [
            'competition' => $competition,
        ]);
    }
}