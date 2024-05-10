<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\TeamType;
use App\Repository\TeamRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminTeamController extends AbstractController
{
    //accéder aux équipes dans l'admin
    #[Route('/admin/teams/{page<\d+>?1}', name: 'admin_teams_index')]
    public function index(PaginationService $pagination, int $page): Response
    {
        $pagination->setEntityClass(Team::class) // App\Entity\Team string
                ->setPage($page)
                ->setLimit(10);
       

        return $this->render('admin/team/index.html.twig', [
           'pagination' => $pagination
        ]);
    }
    #[Route("/admin/teams/new", name:"admin_teams_create")]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {

        $team = new Team();

        $form = $this->createForm(TeamType::class, $team);

     
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // gestion des images 
            foreach($team->getImages() as $image)
            {
                $image->setTeam($team);
                $manager->persist($image);
            }

            // intégration du user
         ;

            // je persiste mon objet Ad
            $manager->persist($team);
            // j'envoie les persistances dans la bdd
            $manager->flush();

            $this->addFlash(
                'success', 
                "L'équipe du <strong>".$team->getName()."</strong> a bien été enregistrée"
            );

            return $this->redirectToRoute('admin_dashboard_index',[
                'slug' => $team->getSlug()
            ]);

        }

        return $this->render("admin/team/new.html.twig",[
            'myForm' => $form->createView()
        ]);

    }
}
