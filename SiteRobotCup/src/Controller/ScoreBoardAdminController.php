<?php

namespace App\Controller;

use App\Entity\Encounter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for managing the admin scoreboard panel.
 */
class ScoreBoardAdminController extends AbstractController
{
    private const ITEMS_PER_PAGE = 10;

    /**
     * Displays and handles the admin scoreboard panel.
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param int $page Current page number
     * @return Response
     */
    #[Route('/admin/panel-scores/{page}',
        name: 'app_panel_scores_admin',
        requirements: ['page' => '\d+'],
        defaults: ['page' => 1],
        methods: ['GET', 'POST']
    )]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        int $page = 1
    ): Response {
        $encounterRepository = $entityManager->getRepository(Encounter::class);
        $queryBuilder = $this->createEncounterQueryBuilder($encounterRepository);

        $paginationData = $this->getPaginationData(
            $queryBuilder,
            $page,
            self::ITEMS_PER_PAGE
        );

        if ($request->isMethod('POST')) {
            $this->handleScoreSubmission($request, $encounterRepository, $entityManager, $page);
            return $this->redirectToRoute('app_panel_scores_admin', ['page' => $page]);
        }

        return $this->render('panel_scores_admin/index.html.twig', [
            'matches' => $paginationData['matches'],
            'page' => $paginationData['currentPage'],
            'totalPages' => $paginationData['totalPages']
        ]);
    }

    /**
     * Creates the query builder for encounters with team joins.
     *
     * @param mixed $repository
     * @return QueryBuilder
     */
    private function createEncounterQueryBuilder($repository): QueryBuilder
    {
        return $repository->createQueryBuilder('e')
            ->leftJoin('e.teamBlue', 'tb')
            ->leftJoin('e.teamGreen', 'tg')
            ->addSelect('tb', 'tg');
    }

    /**
     * Calculates pagination data.
     *
     * @param QueryBuilder $queryBuilder
     * @param int $page
     * @param int $limit
     * @return array
     */
    private function getPaginationData(
        QueryBuilder $queryBuilder,
        int $page,
        int $limit
    ): array {
        $totalMatches = count($queryBuilder->getQuery()->getResult());
        $totalPages = max(1, ceil($totalMatches / $limit));
        $currentPage = max(1, min($page, $totalPages));
        $offset = ($currentPage - 1) * $limit;

        $matches = $queryBuilder
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return [
            'matches' => $matches,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
        ];
    }

    /**
     * Handles the submission of scores.
     *
     * @param Request $request
     * @param mixed $repository
     * @param EntityManagerInterface $entityManager
     * @param int $page
     * @return void
     */
    private function handleScoreSubmission(
        Request $request,
        $repository,
        EntityManagerInterface $entityManager,
        int $page
    ): void {
        try {
            $scores = $request->request->all('scores');
            
            if (!is_array($scores)) {
                throw new \InvalidArgumentException('Invalid scores data format');
            }

            $this->updateScores($scores, $repository);
            $entityManager->flush();
            
            $this->addFlash('success', 'Les scores ont été mis à jour avec succès');
        } catch (\Exception $e) {
            $this->addFlash('error', 
                'Une erreur est survenue lors de la mise à jour des scores : ' 
                . $e->getMessage()
            );
        }
    }

    /**
     * Updates encounter scores.
     *
     * @param array $scores
     * @param mixed $repository
     * @return void
     */
    private function updateScores(array $scores, $repository): void
    {
        foreach ($scores as $matchId => $score) {
            if (!$this->isValidScore($score)) {
                continue;
            }

            $match = $repository->find($matchId);
            if (!$match) {
                continue;
            }

            $match->setScoreBlue((int)$score['blue']);
            $match->setScoreGreen((int)$score['green']);
        }
    }

    /**
     * Validates score data.
     *
     * @param mixed $score
     * @return bool
     */
    private function isValidScore($score): bool
    {
        return is_array($score) 
            && isset($score['blue'], $score['green'])
            && is_numeric($score['blue'])
            && is_numeric($score['green']);
    }
}
