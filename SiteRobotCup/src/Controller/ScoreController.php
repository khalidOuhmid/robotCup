<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\TEncounterEnc;
use App\Entity\TTeamTem;
use App\Entity\TChampionshipChp;

class ScoreController extends AbstractController
{
    #[Route('/score', name: 'app_score')]
    public function index(): Response
    {
        return $this->render('score/index.html.twig', [
            'controller_name' => 'ScoreController',
        ]);
    }

    public function importScores(Request $request, EntityManagerInterface $em): JsonResponse|RedirectResponse
    {
        // Ensure only ADMIN users can access this route
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Get JSON data from the request
        $file = $request->files->get('scores_file');
    
        if (!$file) {
            return new JsonResponse(['error' => 'Invalid file type, only JSON is allowed'], 400);
        }
    
        // Check if the file is a valid JSON file
        if ($file->getClientMimeType() !== 'application/json') {
            return new JsonResponse(['error' => 'Invalid file type, only JSON is allowed'], 400);
        }
    
        $jsonContent = file_get_contents($file->getRealPath());
        $data = json_decode($jsonContent, true);

        if($data === null){
            return new JsonResponse(['error' => 'data = null '], 400);
        }

        // Validate JSON structure
        if (!isset($data['encounters']) || !is_array($data['encounters'])) {
            return new JsonResponse(['error' => 'Invalid JSON format'], 400);
        }

        foreach ($data['encounters'] as $encounterData) {
            // Basic validation for required fields
            if (!isset($encounterData['team_blue'], $encounterData['team_green'], $encounterData['score_blue'], $encounterData['score_green'], $encounterData['state'], $encounterData['championship'])) {
                continue;
            }

            $teamBlue = $em->getRepository(TTeamTem::class)->findOneBy(['name' => $encounterData['team_blue']]);
            $teamGreen = $em->getRepository(TTeamTem::class)->findOneBy(['name' => $encounterData['team_green']]);

            
            if (!$teamBlue || !$teamGreen) {
                return new JsonResponse(['error' => 'One or both teams not found'], 404);
            }

            $championship = $em->getRepository(TChampionshipChp::class)->findOneBy(['name' => $encounterData['championship']]);
            // Create new Encounter entity
            $encounter = new TEncounterEnc();
            $encounter->setTeamBlue($teamBlue);
            $encounter->setTeamGreen($teamGreen);
            $encounter->setScoreBlue($encounterData['score_blue']);
            $encounter->setScoreGreen($encounterData['score_green']);
            $encounter->setState($encounterData['state']);
            $encounter->setChampionship($championship);

            // Persist to database
            try{
                $em->persist($encounter);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
            }
        }

        // Flush all changes to the database
        $em->flush();

        return $this->redirectToRoute('app_score');
    }
}
