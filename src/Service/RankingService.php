<?php

namespace App\Service;

class RankingService
{
    public function calculateRanking(array $ranking, ?string $filter): array
    {
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
                    // Appliquer le filtre pour la meilleure attaque
                    usort($ranking, function($a, $b) {
                        return $b->getGoalsFor() <=> $a->getGoalsFor();
                    });
                    break;
                case 'best_defense':
                    // Appliquer le filtre pour la meilleure défense
                    usort($ranking, function($a, $b) {
                        return $a->getGoalsAgainst() <=> $b->getGoalsAgainst();
                    });
                    break;
                case 'most_wins':
                    // Appliquer le filtre pour le plus grand nombre de victoires
                    usort($ranking, function($a, $b) {
                        return $b->getWins() <=> $a->getWins();
                    });
                    break;
                default:
                    // Si le filtre spécifié n'est pas reconnu, renvoyer toutes les données de classement
                    break;
            }
        }

        return $ranking;
    }
}
