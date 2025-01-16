<?php

namespace App\Controller;

use App\Entity\Encounter;
use App\Entity\TimeSlot;
use App\Form\EncounterForm;
use App\Form\GenerateChampionshipMatchesType;
use App\Service\ChampionshipScheduler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller for managing encounters/time slots.
 */
#[IsGranted('ROLE_ADMIN')]
class EncounterManagementController extends AbstractController
{
    /**
     * Displays the encounter calendar and management interface.
     */
    #[Route('/admin/encounters', name: 'app_admin_encounters')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $encounters = $entityManager
            ->getRepository(Encounter::class)
            ->createQueryBuilder('e')
            ->leftJoin('e.timeSlot', 't')
            ->orderBy('t.dateBegin', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('admin/encounter/index.html.twig', [
            'encounters' => $encounters
        ]);
    }

    /**
     * Creates a new encounter.
     */
    #[Route('/admin/encounter/new', name: 'app_admin_encounter_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $encounter = new Encounter();
        $timeSlot = new TimeSlot();
        $encounter->setTimeSlot($timeSlot);
        
        $form = $this->createForm(EncounterForm::class, $encounter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($timeSlot);
                $entityManager->persist($encounter);
                $entityManager->flush();
                
                $this->addFlash('success', 'Rencontre créée avec succès');
                return $this->redirectToRoute('app_admin_encounters');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la création de la rencontre');
            }
        }

        return $this->render('admin/encounter/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Edits an existing encounter.
     */
    #[Route('/admin/encounter/{id}/edit', name: 'app_admin_encounter_edit')]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        Encounter $encounter
    ): Response {
        $form = $this->createForm(EncounterForm::class, $encounter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Rencontre modifiée avec succès');
                return $this->redirectToRoute('app_admin_encounters');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la modification de la rencontre');
            }
        }

        return $this->render('admin/encounter/edit.html.twig', [
            'form' => $form->createView(),
            'encounter' => $encounter
        ]);
    }

    /**
     * Deletes an encounter.
     */
    #[Route('/admin/encounter/{id}/delete', name: 'app_admin_encounter_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        EntityManagerInterface $entityManager,
        Encounter $encounter
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$encounter->getId(), $request->request->get('_token'))) {
            try {
                $entityManager->remove($encounter);
                $entityManager->flush();
                $this->addFlash('success', 'Rencontre supprimée avec succès');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la suppression de la rencontre');
            }
        }

        return $this->redirectToRoute('app_admin_encounters');
    }

    #[Route('/admin/encounter/generateChampionship', name: 'app_admin_encounter_generate_championship')]
    public function generateChampionship(
        Request $request, 
        ChampionshipScheduler $scheduler,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(GenerateChampionshipMatchesType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            $startTime = $data['startTime'];
            $endTime = $data['endTime'];
            $matchDuration = $data['matchDuration'];
            $maxMatches = $data['maxMatches'];

            try {
                // Utiliser directement l'ID du terrain
                $fieldId = $data['field']->getId();
                error_log("Terrain sélectionné: " . $fieldId);

                // Récupérer le nombre d'équipes du championnat avec la bonne structure SQL
                $teamsCount = $entityManager->getConnection()
                    ->executeQuery(
                        'SELECT COUNT(DISTINCT t.TEM_ID) 
                         FROM T_TEAM_TEM t
                         JOIN T_COMPETITION_CMP c ON t.CMP_ID = c.CMP_ID
                         JOIN T_CHAMPIONSHIP_CHP ch ON ch.CMP_ID = c.CMP_ID
                         WHERE ch.CHP_ID = :championshipId',
                        ['championshipId' => $data['championship']->getId()]
                    )
                    ->fetchOne();

                error_log("Nombre d'équipes trouvées: " . $teamsCount);
                
                if ($teamsCount < 2) {
                    throw new \Exception("Il faut au moins 2 équipes dans le championnat pour générer des rencontres");
                }

                $totalMatchesNeeded = ($teamsCount * ($teamsCount - 1)) / 2; // Formule pour le nombre total de matchs
                error_log("Nombre total de matchs nécessaires: $totalMatchesNeeded");

                // Calculer combien de créneaux on peut avoir dans la plage horaire donnée
                $timeSlotDuration = ($endTime->getTimestamp() - $startTime->getTimestamp()) / 60; // en minutes
                $possibleSlots = floor($timeSlotDuration / $matchDuration);
                
                // Prendre le minimum entre le nombre de matchs nécessaires et le maximum spécifié
                $matchesToSchedule = min($totalMatchesNeeded, $maxMatches, $possibleSlots);
                error_log("Nombre de matchs à programmer: $matchesToSchedule");

                $timeSlots = [];
                $currentStart = clone $startTime;

                for ($i = 0; $i < $matchesToSchedule; $i++) {
                    // S'assurer que le match ne dépasse pas la fin du créneau global
                    $currentEnd = clone $currentStart;
                    $currentEnd->modify('+' . $matchDuration . ' minutes');

                    if ($currentEnd > $endTime) {
                        break;
                    }

                    $timeSlots[] = [
                        'start' => clone $currentStart,
                        'end' => clone $currentEnd,
                        'count' => 1
                    ];

                    // Avancer au prochain créneau
                    $currentStart = clone $currentEnd;
                }

                error_log("Nombre de créneaux créés: " . count($timeSlots));
                error_log("Premier créneau: " . $timeSlots[0]['start']->format('Y-m-d H:i:s') . " - " . $timeSlots[0]['end']->format('Y-m-d H:i:s'));
                if (count($timeSlots) > 1) {
                    error_log("Dernier créneau: " . end($timeSlots)['start']->format('Y-m-d H:i:s') . " - " . end($timeSlots)['end']->format('Y-m-d H:i:s'));
                }

                $scheduler->generateMatches(
                    $data['championship']->getId(),
                    $timeSlots,
                    $fieldId  // Passer directement l'ID du terrain
                );

                $entityManager->flush();
                $this->addFlash('success', sprintf('%d rencontres sur %d ont été générées avec succès.', 
                    count($timeSlots), 
                    $totalMatchesNeeded
                ));
                return $this->redirectToRoute('app_admin_encounters');
            } catch (\Exception $e) {
                error_log("Erreur: " . $e->getMessage());
                $this->addFlash('error', 'Erreur lors de la génération des rencontres : ' . $e->getMessage());
            }
        }

        return $this->render('admin/encounter/generate_championship.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
