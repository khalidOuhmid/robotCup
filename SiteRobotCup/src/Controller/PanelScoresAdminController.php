<?php

namespace App\Controller;

use App\Entity\TEncounterEnc;  // Utilisez la bonne entité ici
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanelScoresAdminController extends AbstractController
{
    #[Route('/admin/panel-scores/{page}', name: 'app_panel_scores_admin', requirements: ['page' => '\d+'], defaults: ['page' => 1])]
    public function index(Request $request, EntityManagerInterface $entityManager, int $page = 1): Response
    {
        $repository = $entityManager->getRepository(TEncounterEnc::class);
        
        // Construction de la requête avec les jointures
        $qb = $repository->createQueryBuilder('e')
            ->leftJoin('e.teamBlue', 'tb')
            ->leftJoin('e.teamGreen', 'tg')
            ->addSelect('tb', 'tg');

        // Pagination
        $limit = 10;
        $totalMatches = count($qb->getQuery()->getResult());
        $totalPages = max(1, ceil($totalMatches / $limit));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $limit;

        $matches = $qb->setFirstResult($offset)
                     ->setMaxResults($limit)
                     ->getQuery()
                     ->getResult();

        // Gestion des scores soumis
        if ($request->isMethod('POST')) {
            try {
                // Récupération des scores depuis la requête
                $scores = $request->request->all('scores');
                
                if (is_array($scores)) {
                    foreach ($scores as $matchId => $score) {
                        // Recherche du match par son ID
                        $match = $repository->find($matchId);
                        
                        // Vérification que le match existe et que les scores sont valides
                        if ($match && is_array($score) && 
                            isset($score['blue'], $score['green']) && 
                            is_numeric($score['blue']) && 
                            is_numeric($score['green'])) {
                            
                            // Mise à jour des scores
                            $match->setScoreBlue((int)$score['blue']);
                            $match->setScoreGreen((int)$score['green']);
                        }
                    }
                }
                
                // Sauvegarde des modifications
                $entityManager->flush();
                $this->addFlash('success', 'Les scores ont été mis à jour avec succès');
            } catch (\Exception $e) {
                // Gestion des erreurs
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour des scores : ' . $e->getMessage());
            }

            // Redirection vers la route d'administration des scores
            return $this->redirectToRoute('app_panel_scores_admin', ['page' => $page]);
        }

        return $this->render('panel_scores_admin/index.html.twig', [
            'matches' => $matches,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }
}