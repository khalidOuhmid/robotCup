<?php

namespace App\Controller;

use App\Entity\Encounter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for managing and displaying encounters.
 */
class EncounterListController extends AbstractController
{
    /**
     * Displays a paginated list of encounters.
     *
     * @param int $page Current page number
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/encounters/{page}', 
        name: 'app_encounters',
        requirements: ['page' => '\d+'],
        defaults: ['page' => 1]
    )]
    public function showEncounters(int $page, EntityManagerInterface $entityManager): Response
    {
        $itemsPerPage = 1;
        $encounterRepository = $entityManager->getRepository(Encounter::class);
        $totalEncounters = $encounterRepository->count([]);
        $totalPages = (int) ceil($totalEncounters / $itemsPerPage);

        // Ensure page number is within valid range
        $currentPage = $this->validatePageNumber($page, $totalPages);
        
        // Calculate offset for pagination
        $offset = ($currentPage - 1) * $itemsPerPage;
        
        // Fetch encounters for current page
        $encounters = $encounterRepository->findBy(
            [],
            null,
            $itemsPerPage,
            $offset
        );

        return $this->render('encounters/index.html.twig', [
            'encounters' => $encounters,
            'page' => $currentPage,
            'totalPages' => $totalPages,
        ]);
    }

    /**
     * Exports all encounters as JSON file.
     *
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/encounters/export', name: 'app_encounters_export')]
    public function exportEncounters(EntityManagerInterface $entityManager): JsonResponse
    {
        $encounterRepository = $entityManager->getRepository(Encounter::class);
        $encounters = $encounterRepository->findAll();
        
        $exportData = $this->prepareEncountersForExport($encounters);
        
        return $this->createJsonExportResponse($exportData);
    }

    /**
     * Validates and corrects the page number.
     *
     * @param int $page Requested page number
     * @param int $totalPages Total number of pages
     * @return int Validated page number
     */
    private function validatePageNumber(int $page, int $totalPages): int
    {
        return max(1, min($page, $totalPages));
    }

    /**
     * Prepares encounters data for export.
     *
     * @param array $encounters Array of Encounter entities
     * @return array Formatted encounter data
     */
    private function prepareEncountersForExport(array $encounters): array
    {
        $exportData = [];
        
        foreach ($encounters as $encounter) {
            $timeSlot = $encounter->getTimeSlot();
            $exportData[] = [
                'championshipId' => $encounter->getChampionship()?->getId(),
                'teamBlue' => $encounter->getTeamBlue()->getName(),
                'scoreBlue' => $encounter->getScoreBlue(),
                'penaltyBlue' => $encounter->getPenaltyBlue(),
                'teamGreen' => $encounter->getTeamGreen()->getName(),
                'scoreGreen' => $encounter->getScoreGreen(),
                'penaltyGreen' => $encounter->getPenaltyGreen(),
                'state' => $encounter->getState(),
                'comment' => $encounter->getComment(),
                'dateBegin' => $timeSlot?->getDateBegin()->format('Y-m-d H:i:s'),
                'dateEnd' => $timeSlot?->getDateEnd()->format('Y-m-d H:i:s'),
            ];
        }

        return $exportData;
    }

    /**
     * Creates JSON response for file download.
     *
     * @param array $data Data to be exported
     * @return JsonResponse
     */
    private function createJsonExportResponse(array $data): JsonResponse
    {
        $jsonData = json_encode($data, JSON_PRETTY_PRINT);
        $response = new JsonResponse($jsonData, Response::HTTP_OK, [], true);
        
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'rencontres.json'
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
