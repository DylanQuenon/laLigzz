<?php

namespace App\Controller;

use App\Entity\Team;
use App\Repository\NewsRepository;
use App\Repository\TeamRepository;
use App\Service\PaginationService;
use App\Repository\MatchesRepository;
use App\Repository\RankingRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TeamController extends AbstractController
{
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

    #[Route("/teams/{slug}", name: "teams_show")]
    public function show(string $slug, Team $team, MatchesRepository $repo, RankingRepository $rankRepo, NewsRepository $newsRepo): Response
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

        $ranking = $rankRepo->findBy([], ['points' => 'DESC']);
        $classement = $rankRepo->findOneBy(['team' => $team]);
        $teamRank = null;
        foreach ($ranking as $index => $rankedTeam) {
            if ($rankedTeam->getTeam() === $team) {
                $teamRank = $index + 1;
                break;
            }
        }

        return $this->render("team/show.html.twig", [
            'team' => $team,
            'lastMatches' => $lastMatches,
            'teamRank' => $teamRank,
            'ranking'=>$classement,
            'lastNews' => $lastNews,
        ]);
    }
}
