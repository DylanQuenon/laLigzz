<?php

namespace App\Controller;

use App\Entity\News;
use App\Repository\NewsRepository;
use App\Service\PaginationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NewsController extends AbstractController
{
    #[Route('/news/{page<\d+>?1}', name: 'news_index')]
    public function index(NewsRepository $repo,PaginationService $pagination, int $page): Response
    {
        $limit = 9;

        $pagination->setEntityClass(News::class)
        ->setPage($page)
        ->setLimit($limit);
        $totalTeams = $repo->count([]);
    
        // Vérifie si le nombre total de voitures et la limite sont non nuls avant de calculer le nombre de pages
        $totalPages = ($totalTeams > 0 && $limit > 0) ? ceil($totalTeams / $limit) : 1;
        if ($page < 1 || $page > $totalPages) {
            // Redirige vers la dernière page
            return $this->redirectToRoute('team_index', ['page' => $totalPages]);
        }
        return $this->render('news/index.html.twig', [
            'pagination' => $pagination
        ]);
    }
}
