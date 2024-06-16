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
        $filter = $request->query->get('filter');
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

        if (!$filter) {
            // Trier les équipes en fonction de leurs points et de leur différence de buts en cas d'égalité de points
            usort($ranking, function($a, $b) {
                // Calculer la différence de buts pour chaque équipe
                $goalDifferenceA = $a->getGoalsFor() - $a->getGoalsAgainst();
                $goalDifferenceB = $b->getGoalsFor() - $b->getGoalsAgainst();

                if ($a->getPoints() === $b->getPoints()) {
                    // En cas d'égalité de points, comparer la différence de buts
                    return $goalDifferenceB <=> $goalDifferenceA;
                }

                // Sinon, comparer les points
                return $b->getPoints() <=> $a->getPoints();
            });
        } else {
            // Appliquer le filtre approprié
            switch ($filter) {
                case 'best_attack':
                    // Appliquer le filtre pour la meilleure attaque (exemple fictif)
                    usort($ranking, function($a, $b) {
                        return $b->getGoalsFor() <=> $a->getGoalsFor();
                    });
                    break;
                case 'best_defense':
                    // Appliquer le filtre pour la meilleure défense (exemple fictif)
                    usort($ranking, function($a, $b) {
                        return $a->getGoalsAgainst() <=> $b->getGoalsAgainst();
                    });
                    break;
                case 'most_wins':
                    // Appliquer le filtre pour le plus grand nombre de victoires (exemple fictif)
                    usort($ranking, function($a, $b) {
                        return $b->getWins() <=> $a->getWins();
                    });
                    break;
                default:
                    // Si le filtre spécifié n'est pas reconnu, renvoyer toutes les données de classement
                    break;
            }
        }

        // Passer les données triées ou filtrées au modèle Twig pour affichage
        return $this->render('ranking/index.html.twig', [
            'ranking' => $ranking,
            'lastMatches' => $lastMatches,
            'lastNews' => $lastNews,
            'goalsByMatchday' => $goalsByMatchday,
        ]);
    }
}
