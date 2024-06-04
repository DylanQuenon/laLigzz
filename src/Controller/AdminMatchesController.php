<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\Matches;
use App\Entity\Ranking;
use App\Form\MatchesType;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminMatchesController extends AbstractController
{
    /**
     * Affiche tous les matchs
     *
     * @param PaginationService $pagination
     * @param integer $page
     * @return Response
     */
    #[Route('/admin/matches/{page<\d+>?1}', name: 'admin_matches_index')]
    public function index(PaginationService $pagination, int $page): Response
    {
        $pagination->setEntityClass(Matches::class) // App\Entity\Team string
                ->setPage($page)
                ->setLimit(9);
       

        return $this->render('admin/matches/index.html.twig', [
           'pagination' => $pagination
        ]);
    }
    /**
     * Fonction pour créer un match
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/games/new', name: 'admin_matches_create')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $match = new Matches();
        $form = $this->createForm(MatchesType::class, $match);     
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // je persiste mon objet match

            if ($match->getHomeTeam() === $match->getAwayTeam()) {
                $this->addFlash('danger', 'Les équipes ne peuvent pas être les mêmes.');
                return $this->redirectToRoute('admin_matches_index');
            }

            // Vérifier si les équipes ne se sont pas déjà affrontées deux fois cette saison
            $seasonMatches = $this->getDoctrine()->getRepository(Matches::class)->findSeasonMatches($match->getHomeTeam(), $match->getAwayTeam(), '2023-08-01', '2024-06-30');
            if (count($seasonMatches) >= 2) {
                $this->addFlash('danger', 'Ces équipes se sont déjà affrontées deux fois cette saison.');
                return $this->redirectToRoute('admin_matches_index');
            }
            $homeTeam = $match->getHomeTeam();
            $awayTeam = $match->getAwayTeam();
            $homeTeamGoals = $match->getHomeTeamGoals();
            $awayTeamGoals = $match->getAwayTeamGoals();

                // Mise à jour du classement pour l'équipe à domicile
        $this->updateTeamRanking($homeTeam, $homeTeamGoals, $awayTeamGoals, $manager);

        // Mise à jour du classement pour l'équipe à l'extérieur
        $this->updateTeamRanking($awayTeam, $awayTeamGoals, $homeTeamGoals, $manager);



            $manager->persist($match);
            // j'envoie les persistances dans la bdd
            $manager->flush();

            $this->addFlash(
                'success', 
                "Le match <strong>".$match->getId()."</strong> a bien été enregistré"
            );

            return $this->redirectToRoute('admin_matches_index',[
                'id' => $match->getId()
            ]);
        }
        return $this->render("admin/matches/new.html.twig",[
            'myForm' => $form->createView()
        ]);
    }


    /**
     * Récupère le logo des équipes choisies
     *
     * @param [type] $id
     * @param Team $team
     * @return JsonResponse
     */
    #[Route('/admin/teams/json/{id}', name: 'team_logo')]
    public function getTeamLogo($id, Team $team): JsonResponse
    {
        if (!$team) {
            return new JsonResponse(['error' => 'Team not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Vous devez ajuster ce chemin selon l'emplacement de vos logos dans le projet
        $logoUrl = '/uploads/' . $team->getLogo();

        return new JsonResponse(['logoUrl' => $logoUrl]);
    }


    /**
     * Permet de modifier les matchs
     *
     * @param Matches $match
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/admin/matches/{id}/edit", name: "admin_matches_edit")]
    public function edit(Matches $match, Request $request, EntityManagerInterface $manager): Response
    {
        $oldHomeTeam = $match->getHomeTeam();
        $oldAwayTeam = $match->getAwayTeam();
        $oldHomeTeamGoals = $match->getHomeTeamGoals();
        $oldAwayTeamGoals = $match->getAwayTeamGoals();

        $form = $this->createForm(MatchesType::class, $match);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $match->setStadium('');
            // Annuler les effets du match existant sur le classement
            $this->cancelTeamRanking($oldHomeTeam, $oldHomeTeamGoals, $oldAwayTeamGoals, $manager);
            $this->cancelTeamRanking($oldAwayTeam, $oldAwayTeamGoals, $oldHomeTeamGoals, $manager);

            $manager->persist($match);
            $manager->flush();

            // Mettre à jour le classement avec les nouvelles données du match
            $this->updateTeamRanking($match->getHomeTeam(), $match->getHomeTeamGoals(), $match->getAwayTeamGoals(), $manager);
            $this->updateTeamRanking($match->getAwayTeam(), $match->getAwayTeamGoals(), $match->getHomeTeamGoals(), $manager);

            $this->addFlash(
                'success',
                "Le match <strong>".$match->getId()."</strong> a bien été modifié"
            );

            return $this->redirectToRoute('admin_matches_index');
        }

        return $this->render("admin/matches/edit.html.twig", [
            "match" => $match,
            "myForm" => $form->createView()
        ]);
    }
    /**
     * Effacer les matchs
     *
     * @param Matches $match
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/admin/matches/{id}/delete", name: "admin_matches_delete")]
    public function delete(Matches $match, EntityManagerInterface $manager): Response
    {
        $oldHomeTeamGoals = $match->getHomeTeamGoals();

    $oldAwayTeamGoals = $match->getAwayTeamGoals();


    $this->cancelTeamRanking($match->getHomeTeam(), $oldHomeTeamGoals, $oldAwayTeamGoals, $manager);

    $this->cancelTeamRanking($match->getAwayTeam(), $oldAwayTeamGoals, $oldHomeTeamGoals, $manager);
        $this->addFlash(
            "success",
            "Le match <strong>".$match->getId()."</strong> a bien été supprimé"
        );
        $manager->remove($match);
        $manager->flush();
        
        return $this->redirectToRoute('admin_matches_index');
    }

    private function updateRanking(Matches $match, EntityManagerInterface $manager): void
    {
        $homeTeam = $match->getHomeTeam();
        $awayTeam = $match->getAwayTeam();
        $homeTeamGoals = $match->getHomeTeamGoals();
        $awayTeamGoals = $match->getAwayTeamGoals();

        // Met à jour le classement de l'équipe à domicile
        $this->updateTeamRanking($homeTeam, $homeTeamGoals, $awayTeamGoals, $manager);

        // Met à jour le classement de l'équipe à l'extérieur
        $this->updateTeamRanking($awayTeam, $awayTeamGoals, $homeTeamGoals, $manager);
    }

    /**
     * Met à jour le classement d'une équipe en fonction du résultat du match
     *
     * @param Team $team
     * @param int $goalsFor
     * @param int $goalsAgainst
     * @param EntityManagerInterface $manager
     */
    private function updateTeamRanking(Team $team, int $goalsFor, int $goalsAgainst, EntityManagerInterface $manager): void
    {
        $ranking = $team->getRanking();

        // if (!$ranking) {
        //     $ranking = new Ranking();
        //     $ranking->setTeam($team);
        // }

        $ranking->setMatchesPlayed($ranking->getMatchesPlayed() + 1);
        $ranking->setGoalsFor($ranking->getGoalsFor() + $goalsFor);
        $ranking->setGoalsAgainst($ranking->getGoalsAgainst() + $goalsAgainst);

        if ($goalsFor > $goalsAgainst) {
            $ranking->setWins($ranking->getWins() + 1);
            $ranking->setPoints($ranking->getPoints() + 3);
        } elseif ($goalsFor < $goalsAgainst) {
            $ranking->setLosses($ranking->getLosses() + 1);
        } else {
            $ranking->setDraws($ranking->getDraws() + 1);
            $ranking->setPoints($ranking->getPoints() + 1);
        }

        $manager->persist($ranking);
        $manager->flush();
    }

    /**
 * Annule les anciennes valeurs du classement d'une équipe
 *
 * @param Team $team
 * @param int $oldGoalsFor
 * @param int $oldGoalsAgainst
 * @param EntityManagerInterface $manager
 */
private function cancelTeamRanking(Team $team, int $oldGoalsFor, int $oldGoalsAgainst, EntityManagerInterface $manager): void
{
    $ranking = $team->getRanking();

    $ranking->setMatchesPlayed($ranking->getMatchesPlayed() - 1);
    $ranking->setGoalsFor($ranking->getGoalsFor() - $oldGoalsFor);
    $ranking->setGoalsAgainst($ranking->getGoalsAgainst() - $oldGoalsAgainst);

    if ($oldGoalsFor > $oldGoalsAgainst) {
        $ranking->setWins($ranking->getWins() - 1);
        $ranking->setPoints($ranking->getPoints() - 3);
    } elseif ($oldGoalsFor < $oldGoalsAgainst) {
        $ranking->setLosses($ranking->getLosses() - 1);
    } else {
        $ranking->setDraws($ranking->getDraws() - 1);
        $ranking->setPoints($ranking->getPoints() - 1);
    }

    $manager->persist($ranking);
    $manager->flush();
}

 
}
