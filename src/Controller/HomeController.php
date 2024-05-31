<?php

namespace App\Controller;

use App\Service\StatsService;
use App\Repository\NewsRepository;
use App\Repository\TeamRepository;
use App\Repository\MatchesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * Affiche la page home
     *
     * @param TeamRepository $team
     * @param NewsRepository $new
     * @return Response
     */
    #[Route('/', name: 'homepage')]
    public function index(TeamRepository $team, NewsRepository $new, StatsService $stats,MatchesRepository $repoMatches): Response
    {
        $teams = $team->findAll();
        $totalGoals= $stats->getAllGoals();
        $totalGames= $stats->getAllGames();
        $totalPoints= $stats->getAllPoints();
        $lastMatches = $repoMatches->findBy([], ['date' => 'DESC'], 3);
        $lastNews = $new->findBy([], ['createdAt' => 'DESC'], 3);
        return $this->render('home.html.twig', [
            'teams' => $teams,
            'lastNews'=>$lastNews,
            'lastMatches'=>$lastMatches,
            'stats' => [
                'allGoals' => $totalGoals,
                'allGames'=> $totalGames,
                'allPoints'=> $totalPoints,
          
            ],
           
            
        ]);
    }
}
