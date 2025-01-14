<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScoreImportController extends AbstractController
{
    #[Route('/import-score', name: 'import_score')]
    public function importScore(): Response
    {
        return $this->render('score_import/import.html.twig');
    }
}
