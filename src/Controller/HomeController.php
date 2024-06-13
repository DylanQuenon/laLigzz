<?php

namespace App\Controller;

use App\Service\StatsService;
use App\Service\RankingService;
use App\Repository\NewsRepository;
use App\Repository\TeamRepository;
use App\Repository\MatchesRepository;
use App\Repository\RankingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * Affiche la page home
     *
     * @param TeamRepository $team
     * @param NewsRepository $new
     * @param StatsService $stats
     * @param MatchesRepository $repoMatches
     * @param RankingService $rankingService
     * @param Request $request
     * @param RankingRepository $rankingRepo
     * @return Response
     */
    #[Route('/', name: 'homepage')]
    public function index(TeamRepository $team, NewsRepository $new, StatsService $stats, MatchesRepository $repoMatches, RankingService $rankingService, Request $request,RankingRepository $rankingRepo): Response
    {
        $ranking = $rankingRepo->findAll();
        $teams = $team->findAll();
        $totalGoals = $stats->getAllGoals();
        $totalGames = $stats->getAllGames();
        $totalPoints = $stats->getAllPoints();
        $lastMatches = $repoMatches->findBy([], ['date' => 'DESC'], 3);
        $lastNews = $new->findBy([], ['createdAt' => 'DESC'], 3);
        $filter = $request->query->get('filter', '');
        $sortedRanking = $rankingService->calculateRanking($ranking,$filter);

        return $this->render('home.html.twig', [
            'teams' => $teams,
            'lastNews' => $lastNews,
            'lastMatches' => $lastMatches,
            'ranking' => $sortedRanking,
            'stats' => [
                'allGoals' => $totalGoals,
                'allGames' => $totalGames,
                'allPoints' => $totalPoints,
            ],
        ]);
    }
   
    #[Route('/mentions-legales', name: 'mentions')]
    public function mentionsLegales(): Response
    {
        return $this->render('legals/index.html.twig');
    }

    
}
