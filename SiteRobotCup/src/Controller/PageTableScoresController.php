<?php

namespace App\Controller;

use App\Repository\TeamRepository;
use App\Repository\EncounterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Team;
use App\Entity\Encounter;

class PageTableScoresController extends AbstractController
{
    /**
     * Displays the scoreboard page with team rankings and encounters.
     *
     * @param TeamRepository $teamRepository
     * @param EncounterRepository $encounterRepository
     * @param Request $request
     * @return Response
     */
    #[Route('/scores', name: 'app_page_tableau_scores', methods: ['GET'])]
    #[Route('/', name: 'app_default', methods: ['GET'])]
    public function index(
        TeamRepository $teamRepository,
        EncounterRepository $encounterRepository,
        Request $request
    ): Response {
        // Préparer les données des équipes et des rencontres
        $teams = $this->prepareTeamsData($teamRepository);
        $encounters = $this->prepareEncountersData($encounterRepository);

        // Déterminer le template à utiliser
        $template = $this->determineTemplate($request->getRequestUri());

        // Récupérer les équipes de l'utilisateur si connecté
        $user = $this->getUser();
        if ($user) {
            $userTeams = $teamRepository->findBy(['user' => $user]);
            $userTeamIds = array_map(fn(Team $team) => $team->getId(), $userTeams);
        } else {
            $userTeamIds = [];
        }

        // Rendre le template avec les données des équipes et des rencontres
        return $this->render($template, [
            'teams' => $teams,
            'userTeamIds' => $userTeamIds,
            'encounters' => $encounters,
        ]);
    }

    /**
     * Prépare les données des équipes pour l'affichage.
     *
     * @param TeamRepository $teamRepository
     * @return array
     */
    private function prepareTeamsData(TeamRepository $teamRepository): array
    {
        $teamsData = $teamRepository->findByOrderedByScore();

        return array_map(
            [$this, 'transformTeamData'],
            $teamsData,
            array_keys($teamsData)
        );
    }

    /**
     * Transforme les données brutes des équipes en format pour l'affichage.
     *
     * @param array $data Données brutes d'une équipe
     * @param int $index Index du tableau pour le classement
     * @return array Données transformées de l'équipe
     */
    private function transformTeamData(array $data, int $index): array
    {
        return [
            'id' => $data['team_id'],
            'rank' => $index + 1,
            'name' => $data['team_name'],
            'matches_played' => $data['total_matches'],
            'matches_won' => $data['total_wins'],
            'matches_drawn' => $data['total_draws'],
            'matches_lost' => $data['total_losses'],
            'goals' => $data['average_score'],
            'points' => $data['total_points'],
        ];
    }

    /**
     * Prépare les données des rencontres pour l'affichage.
     *
     * @param EncounterRepository $encounterRepository
     * @return array
     */
    private function prepareEncountersData(EncounterRepository $encounterRepository): array
    {
        $encountersData = $encounterRepository->findAll(); // Récupérer toutes les rencontres

        return array_map(
            [$this, 'transformEncounterData'], // Passer l'entité directement
            $encountersData,
            array_keys($encountersData)
        );
    }

    /**
     * Transforme les données brutes des rencontres en format pour l'affichage.
     *
     * @param Encounter $encounter L'entité de rencontre
     * @param int $index Index du tableau pour les rencontres
     * @return array Données transformées de la rencontre
     */
    private function transformEncounterData(Encounter $encounter, int $index): array
    {
        return [
            'teamBlue' => $encounter->getTeamBlue(),
            'scoreBlue' => $encounter->getScoreBlue(),
            'teamGreen' => $encounter->getTeamGreen(), 
            'scoreGreen' => $encounter->getScoreGreen(),
        ];
    }

    /**
     * Détermine quel template utiliser en fonction de l'URI de la requête.
     *
     * @param string $requestUri
     * @return string
     */
    private function determineTemplate(string $requestUri): string
    {
        return $requestUri === '/scores'
            ? 'page_tableau_scores/index.html.twig'
            : 'default/index.html.twig';
    }
}
