<?php

namespace App\Controller;

use App\Entity\Matches;
use App\Service\PaginationService;
use App\Repository\MatchesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MatchesController extends AbstractController
{
    #[Route('/matches/{page<\d+>?1}', name: 'matches_index')]
    public function index(MatchesRepository $repo,PaginationService $pagination, int $page): Response
    {
        $pagination->setEntityClass(Matches::class)
                    ->setPage($page)
                    ->setLimit(9);
                 // Trier par date de création décroissante

    $totalMatches = $repo->count([]);

    // Calculer le nombre total de pages
    $totalPages = ($totalMatches > 0) ? ceil($totalMatches / 9) : 1;

    // Vérifier si la page demandée est valide
    if ($page < 1 || $page > $totalPages) {
        // Rediriger vers la première page
        return $this->redirectToRoute('matches_index', ['page' => 1]);
    }

        return $this->render('matches/index.html.twig', [
            'games' => $pagination->getData(),
            'pagination' => $pagination
        ]);
    }
}
