<?php

namespace App\Controller;

use App\Repository\NewsRepository;
use App\Repository\MatchesRepository;
use App\Repository\RankingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RankingController extends AbstractController
{
    #[Route('/ranking', name: 'app_ranking')]
    public function index(RankingRepository $repo, Request $request, MatchesRepository $repoMatches, NewsRepository $newsRepo): Response
    {
        // Récupérer toutes les données brutes du classement depuis la base de données
        $ranking = $repo->findAll();
        $lastMatches = $repoMatches->findBy([], ['date' => 'DESC'], 3);
        $lastNews = $newsRepo->findBy([], ['createdAt' => 'DESC'], 3);

        // Récupérer tous les matchs
        $matches = $repoMatches->findAll();

        // Regrouper les données de buts par journée
        $goalsByMatchday = [];

        foreach ($matches as $match) {
            $matchday = $match->getJournee();
            if (!isset($goalsByMatchday[$matchday])) {
                $goalsByMatchday[$matchday] = 0;
            }
            $goalsByMatchday[$matchday] += $match->getHomeTeamGoals() + $match->getAwayTeamGoals(); // Assuming getGoalsFor and getGoalsAgainst are the goals scored by each team in the match
        }

        // Trier les équipes en fonction de leurs points et de leur différence de buts en cas d'égalité de points
        usort($ranking, function($a, $b) {
            $goalDifferenceA = $a->getGoalsFor() - $a->getGoalsAgainst();
            $goalDifferenceB = $b->getGoalsFor() - $b->getGoalsAgainst();

            if ($a->getPoints() === $b->getPoints()) {
                return $goalDifferenceB <=> $goalDifferenceA;
            }

            return $b->getPoints() <=> $a->getPoints();
        });

        // Passer les données triées ou filtrées au modèle Twig pour affichage
        return $this->render('ranking/index.html.twig', [
            'ranking' => $ranking,
            'lastMatches' => $lastMatches,
            'lastNews' => $lastNews,
            'goalsByMatchday' => $goalsByMatchday,
        ]);
    }
}
