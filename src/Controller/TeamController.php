<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\Image;
use App\Repository\TeamRepository;
use App\Service\PaginationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TeamController extends AbstractController
{
    /**
     * Récupère l'ensembles des équipes
     *
     * @return Response
     */
    #[Route('/teams/{page<\d+>?1}', name: 'team_index')]
    public function index(TeamRepository $repo, $page, PaginationService $pagination): Response
    {
        $limit = 9;

        $pagination->setEntityClass(Team::class)
        ->setPage($page)
        ->setLimit($limit);
        $totalTeams = $repo->count([]);
    
        // Vérifie si le nombre total de voitures et la limite sont non nuls avant de calculer le nombre de pages
        $totalPages = ($totalTeams > 0 && $limit > 0) ? ceil($totalTeams / $limit) : 1;
        if ($page < 1 || $page > $totalPages) {
            // Redirige vers la dernière page
            return $this->redirectToRoute('team_index', ['page' => $totalPages]);
        }
        
        
     
        return $this->render('team/index.html.twig', [
            'pagination' => $pagination
        ]);
    }
    #[Route("/teams/{slug}", name:"teams_show")]
    public function show(string $slug, Team $team): Response
    {
        // Utilisation de la méthode générique pour récupérer les images spécifiques
        $logoImage = $this->getImageByCaption($team, 'logo_bg');
        $coverImage = $this->getImageByCaption($team, 'cover');
        $newsImage = $this->getImageByCaption($team, 'news_image');
    
        return $this->render("team/show.html.twig", [
            'team' => $team,
            'logoImage' => $logoImage,
            'coverImage' => $coverImage,
            'newsImage' => $newsImage,
        ]);
    }
    private function getImageByCaption(Team $team, string $caption): ?Image
    {
    foreach ($team->getImages() as $image) {
        if ($image->getCaption() === $caption) {
            return $image;
        }
    }

    return null;
}
}
