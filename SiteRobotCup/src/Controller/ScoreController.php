<?php

namespace App\Controller;

use App\Entity\Championship;
use App\Entity\Encounter;
use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller for managing game scores and encounters.
 */
class ScoreController extends AbstractController
{
    /**
     * Displays the score management interface.
     *
     * @return Response
     */
    #[Route('/score', name: 'app_score')]
    public function index(): Response
    {
        return $this->render('score/index.html.twig', [
            'controller_name' => 'ScoreController',
        ]);
    }

    /**
     * Imports scores from a JSON file.
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse|RedirectResponse
     */
    #[Route('/score/import', name: 'app_score_import', methods: ['POST'])]
    public function importScores(
        Request $request, 
        EntityManagerInterface $entityManager
    ): JsonResponse|RedirectResponse {
        $file = $request->files->get('scores_file');

        if (!$this->isValidFile($file)) {
            return new JsonResponse(
                ['error' => 'Invalid file type, only JSON is allowed'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $data = $this->parseJsonFile($file);
        
        if ($data === null) {
            return new JsonResponse(
                ['error' => 'Invalid JSON data'],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!$this->isValidDataStructure($data)) {
            return new JsonResponse(
                ['error' => 'Invalid JSON format'],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $this->processEncounters($data['encounters'], $entityManager);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_score');
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => 'Database error: ' . $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Validates the uploaded file.
     *
     * @param UploadedFile|null $file
     * @return bool
     */
    private function isValidFile(?UploadedFile $file): bool
    {
        return $file !== null && $file->getClientMimeType() === 'application/json';
    }

    /**
     * Parses JSON file content.
     *
     * @param UploadedFile $file
     * @return array|null
     */
    private function parseJsonFile(UploadedFile $file): ?array
    {
        $jsonContent = file_get_contents($file->getRealPath());
        return json_decode($jsonContent, true);
    }

    /**
     * Validates the JSON data structure.
     *
     * @param array|null $data
     * @return bool
     */
    private function isValidDataStructure(?array $data): bool
    {
        return isset($data['encounters']) && is_array($data['encounters']);
    }

    /**
     * Processes encounter data and creates new encounters.
     *
     * @param array $encounters
     * @param EntityManagerInterface $entityManager
     * @throws \Exception
     */
    private function processEncounters(
        array $encounters,
        EntityManagerInterface $entityManager
    ): void {
        foreach ($encounters as $encounterData) {
            if (!$this->isValidEncounterData($encounterData)) {
                continue;
            }

            $teamBlue = $entityManager->getRepository(Team::class)
                ->findOneBy(['name' => $encounterData['team_blue']]);
            $teamGreen = $entityManager->getRepository(Team::class)
                ->findOneBy(['name' => $encounterData['team_green']]);

            if (!$teamBlue || !$teamGreen) {
                throw new \Exception('One or both teams not found');
            }

            $championship = $entityManager->getRepository(Championship::class)
                ->findOneBy(['name' => $encounterData['championship']]);

            $encounter = $this->createEncounter(
                $encounterData,
                $teamBlue,
                $teamGreen,
                $championship
            );
            
            $entityManager->persist($encounter);
        }
    }

    /**
     * Validates encounter data structure.
     *
     * @param array $encounterData
     * @return bool
     */
    private function isValidEncounterData(array $encounterData): bool
    {
        return isset(
            $encounterData['team_blue'],
            $encounterData['team_green'],
            $encounterData['score_blue'],
            $encounterData['score_green'],
            $encounterData['state'],
            $encounterData['championship']
        );
    }

    /**
     * Creates a new Encounter entity from data.
     *
     * @param array $data
     * @param Team $teamBlue
     * @param Team $teamGreen
     * @param Championship $championship
     * @return Encounter
     */
    private function createEncounter(
        array $data,
        Team $teamBlue,
        Team $teamGreen,
        Championship $championship
    ): Encounter {
        $encounter = new Encounter();
        $encounter->setTeamBlue($teamBlue)
            ->setTeamGreen($teamGreen)
            ->setScoreBlue($data['score_blue'])
            ->setScoreGreen($data['score_green'])
            ->setState($data['state'])
            ->setChampionship($championship);

        return $encounter;
    }
}
