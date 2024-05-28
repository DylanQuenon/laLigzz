<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\Image;
use App\Entity\Matches;
use App\Repository\TeamRepository;
use App\Service\PaginationService;
use App\Repository\MatchesRepository;
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
    /**
     * Affiche les équipes individuellement
     *
     * @param string $slug
     * @param Team $team
     * @return Response
     */
    #[Route("/teams/{slug}", name:"teams_show")]
    public function show(string $slug, Team $team, MatchesRepository $repo): Response
    {
        $lastMatches = $repo->createQueryBuilder('m')
        ->where('m.homeTeam = :team OR m.awayTeam = :team')
        ->setParameter('team', $team)
        ->orderBy('m.date', 'ASC')
        ->setMaxResults(5)
        ->getQuery()
        ->getResult();
        return $this->render("team/show.html.twig", [
            'team' => $team,
            'lastMatches' => $lastMatches,
        ]);
    }
}
