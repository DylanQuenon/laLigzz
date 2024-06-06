<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\Team;
use App\Service\RankingService;
use App\Repository\NewsRepository;
use App\Repository\TeamRepository;
use App\Service\PaginationService;
use App\Repository\MatchesRepository;
use App\Repository\RankingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TeamController extends AbstractController
{
    /**
     * Récupère toutes les équipess
     *
     * @param TeamRepository $repo
     * @param [type] $page
     * @param PaginationService $pagination
     * @return Response
     */
    #[Route('/teams/{page<\d+>?1}', name: 'team_index')]
    public function index(TeamRepository $repo, $page, PaginationService $pagination): Response
    {
        $limit = 9;

        $pagination->setEntityClass(Team::class)->setPage($page)->setLimit($limit);
        $totalTeams = $repo->count([]);

        $totalPages = ($totalTeams > 0 && $limit > 0) ? ceil($totalTeams / $limit) : 1;
        if ($page < 1 || $page > $totalPages) {
            return $this->redirectToRoute('team_index', ['page' => $totalPages]);
        }

        return $this->render('team/index.html.twig', [
            'pagination' => $pagination
        ]);
    }


    #[Route("/teams/{slug}/news", name: "news_team_show")]
    public function teamNews(Team $team, NewsRepository $news, TeamRepository $teamRepository): Response
    {
        $newsTeam= $team->getNews();

        return $this->render("team/news.html.twig", [
           'news'=>$newsTeam,
           'team'=>$team
        ]);
    }
    #[Route("/teams/{slug}/matches", name: "matches_team_show")]
    public function teamMatches(Team $team, NewsRepository $news, TeamRepository $teamRepository): Response
    {
        $matchesTeam= $team->getMatches();

        return $this->render("team/matches.html.twig", [
           'games'=>$matchesTeam,
           'team'=>$team
        ]);
    }
    /**
     * Récupère l'équipe individuellement
     *
     * @param string $slug
     * @param Team $team
     * @param MatchesRepository $repo
     * @param RankingRepository $rankRepo
     * @param NewsRepository $newsRepo
     * @param RankingService $rankingService
     * @param Request $request
     * @return Response
     */
    #[Route("/teams/{slug}", name: "teams_show")]
    public function show(string $slug, Team $team, MatchesRepository $repo, RankingRepository $rankRepo, NewsRepository $newsRepo,RankingService $rankingService, Request $request): Response
    {
        $lastNews = $newsRepo->createQueryBuilder('n')
        ->leftJoin('n.team', 't')
        ->where('t = :team')
        ->setParameter('team', $team)
        ->orderBy('n.createdAt', 'DESC')
        ->setMaxResults(3)
        ->getQuery()
        ->getResult();


        $lastMatches = $repo->createQueryBuilder('m')
            ->where('m.homeTeam = :team OR m.awayTeam = :team')
            ->setParameter('team', $team)
            ->orderBy('m.date', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $ranking = $rankRepo->findAll();
        $classement = $rankRepo->findOneBy(['team' => $team]);
        $filter = $request->query->get('filter', '');
        $sortedRanking = $rankingService->calculateRanking($ranking,$filter);

        $teamPosition = null;
        foreach ($sortedRanking as $index => $rankedTeam) {
            if ($rankedTeam->getTeam() === $team) {
                $teamPosition = $index + 1; // Les index commencent à 0, donc on ajoute 1 pour avoir la position réelle
                break;
            }
        }

        return $this->render("team/show.html.twig", [
            'team' => $team,
            'lastMatches' => $lastMatches,
            'teamRank' => $teamPosition,
            'ranking'=>$classement,
            'lastNews' => $lastNews,
        ]);
    }
}
