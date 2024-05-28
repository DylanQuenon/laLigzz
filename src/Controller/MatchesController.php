<?php

// src/Controller/MatchesController.php
namespace App\Controller;

use App\Entity\Matches;
use App\Service\PaginationService;
use App\Repository\MatchesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MatchesController extends AbstractController
{
    #[Route('/matches/{page<\d+>?1}', name: 'matches_index')]
    public function index(MatchesRepository $repo, PaginationService $pagination, int $page): Response
    {
        $pagination->setEntityClass(Matches::class)
                   ->setPage($page)
                   ->setLimit(10);
        
        $totalMatches = $repo->count([]);

        // Calculer le nombre total de pages
        $totalPages = ($totalMatches > 0) ? ceil($totalMatches / 9) : 1;

        // Vérifier si la page demandée est valide
        if ($page < 1 || $page > $totalPages) {
            // Rediriger vers la première page
            return $this->redirectToRoute('matches_index', ['page' => 1]);
        }

        // Obtenir les matchs paginés
        $matches = $pagination->getData();

        // Grouper les matchs par journée
        $groupedMatches = [];
        foreach ($matches as $match) {
            $matchDay = $match->getJournee();
            if (!isset($groupedMatches[$matchDay])) {
                $groupedMatches[$matchDay] = [];
            }
            $groupedMatches[$matchDay][] = $match;
        }

        return $this->render('matches/index.html.twig', [
            'groupedMatches' => $groupedMatches,
            'pagination' => $pagination
        ]);
    }
}
