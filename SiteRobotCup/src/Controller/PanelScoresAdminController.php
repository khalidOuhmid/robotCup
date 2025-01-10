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
        $limit = 10; // Nombre de matchs par page
        $repository = $entityManager->getRepository(TEncounterEnc::class); // Utilisation de TEncounterEnc

        // Total des rencontres
        $totalMatches = $repository->count([]);

        // Calcul du nombre total de pages
        $totalPages = (int) ceil($totalMatches / $limit);

        // Validation de la page demandée
        $page = max(1, min($page, $totalPages));

        // Récupération des matchs pour la page actuelle
        $offset = ($page - 1) * $limit;
        $matches = $repository->findBy([], null, $limit, $offset);

        // Gestion des scores soumis via le formulaire
        if ($request->isMethod('POST')) {
            $scores = $request->request->get('scores', []);

            $valid = true;
            if (is_array($scores)) {
                foreach ($scores as $matchId => $score) {
                    $match = $repository->find($matchId);
                    if ($match) {
                        // Vérification que les deux scores sont présents et non négatifs
                        if (isset($score['blue'], $score['green'])) {
                            $blueScore = (int) $score['blue'];
                            $greenScore = (int) $score['green'];

                            if ($blueScore < 0 || $greenScore < 0) {
                                $this->addFlash('error', 'Les scores ne peuvent pas être négatifs.');
                                $valid = false;
                                break;
                            }

                            // Si tout est valide, on met à jour les scores
                            $match->setScoreBlue($blueScore);
                            $match->setScoreGreen($greenScore);
                            $entityManager->persist($match);
                        } else {
                            $this->addFlash('error', 'Les deux scores doivent être remplis.');
                            $valid = false;
                            break;
                        }
                    }
                }

                if ($valid) {
                    $entityManager->flush();

                    // Message de succès
                    $this->addFlash('success', 'Les scores ont été enregistrés avec succès.');

                    // Redirection pour éviter la double soumission du formulaire
                    return $this->redirectToRoute('app_panel_scores_admin', ['page' => $page]);
                }
            } else {
                $this->addFlash('error', 'Les données envoyées ne sont pas valides.');
            }
        }

        // Rendu de la vue
        return $this->render('panel_scores_admin/index.html.twig', [
            'matches' => $matches,
            'page' => $page,
            'totalPages' => $totalPages,
        ]);
    }
}


