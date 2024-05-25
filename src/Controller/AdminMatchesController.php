<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\Matches;
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
            // je persiste mon objet team
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
        $form = $this->createForm(MatchesType::class, $match);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($match);
            $manager->flush();
    
            $this->addFlash(
                'success',
                "Le match <strong>".$match->getId()."</strong> a bien été modifié"
            );
    
            return $this->redirectToRoute('admin_matches_index');
        }
    
        return $this->render("admin/matches/edit.html.twig",[
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
        $this->addFlash(
            "success",
            "Le match <strong>".$match->getId()."</strong> a bien été supprimé"
        );
        $manager->remove($match);
        $manager->flush();
        
        return $this->redirectToRoute('admin_matches_index');
    }
 
 
}
