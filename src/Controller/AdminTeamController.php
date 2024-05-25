<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\Image;
use App\Form\TeamType;
use App\Form\TeamEditType;
use App\Repository\TeamRepository;
use App\Service\PaginationService;
use App\Service\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminTeamController extends AbstractController
{
  
    /**
     * Récupère les équipes dans l'admin
     *
     * @param PaginationService $pagination
     * @param integer $page
     * @return Response
     */
    #[Route('/admin/teams/{page<\d+>?1}', name: 'admin_teams_index')]
    public function index(PaginationService $pagination, int $page): Response
    {
        $pagination->setEntityClass(Team::class) // App\Entity\Team string
                ->setPage($page)
                ->setLimit(9);
       

        return $this->render('admin/team/index.html.twig', [
           'pagination' => $pagination
        ]);
    }

    /**
     * Permet d'ajouter une équipe
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param FileUploaderService $fileUploader
     * @return Response
     */
    #[Route("/admin/teams/new", name:"admin_teams_create")]
    public function create(Request $request, EntityManagerInterface $manager, FileUploaderService $fileUploader): Response
    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);     
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->handleFileUpload($form, $team, 'logo', $fileUploader);
            $this->handleFileUpload($form, $team, 'logoBackground', $fileUploader);
            $this->handleFileUpload($form, $team, 'cover', $fileUploader);
            $this->handleFileUpload($form, $team, 'newsPicture', $fileUploader);

            // je persiste mon objet team
            $manager->persist($team);
            // j'envoie les persistances dans la bdd
            $manager->flush();

            $this->addFlash(
                'success', 
                "L'équipe du <strong>".$team->getName()."</strong> a bien été enregistrée"
            );

            return $this->redirectToRoute('admin_teams_edit',[
                'slug' => $team->getSlug()
            ]);
        }

        return $this->render("admin/team/new.html.twig",[
            'myForm' => $form->createView()
        ]);
    }

    private function handleFileUpload($form, $team, $field, $fileUploader)
    {
        $file = $form[$field]->getData();
        if ($file) {
            $newFilename = $fileUploader->upload($file);
            $setter = 'set' . ucfirst($field);
            $team->$setter($newFilename);
        }
    }


    /**
     * Permet de modifier une team
     *
     * @param Teams $team
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/admin/teams/{slug}/edit", name: "admin_teams_edit")]
    public function edit(Team $team, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(TeamEditType::class, $team);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($team);
            $manager->flush();
    
            $this->addFlash(
                'success',
                "L'équipe <strong>".$team->getName()."</strong> a bien été modifiée"
            );
    
            return $this->redirectToRoute('admin_teams_index');
        }
    
        return $this->render("admin/team/edit.html.twig",[
            "team" => $team,
            "myForm" => $form->createView()
        ]);
    }
     
    /**
     * Permet d'effacer une team
     *
     * @param Team $team
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/admin/teams/{slug}/delete", name: "admin_teams_delete")]
    public function delete(Team $team, EntityManagerInterface $manager): Response
    {
        if(!empty($team->getLogo()))
        {
            unlink($this->getParameter('uploads_directory').'/'.$team->getLogo());
        }
        if(!empty($team->getLogoBackground()))
        {
            unlink($this->getParameter('uploads_directory').'/'.$team->getLogoBackground());
        }
        if(!empty($team->getCover()))
        {
            unlink($this->getParameter('uploads_directory').'/'.$team->getCover());
        }
        if(!empty($team->getNewsPicture()))
        {
            unlink($this->getParameter('uploads_directory').'/'.$team->getNewsPicture());
        }
        $this->addFlash(
            "success",
            "L'annonce <strong>".$team->getName()."</strong> a bien été supprimée"
        );
        $manager->remove($team);
        $manager->flush();
        
        return $this->redirectToRoute('admin_teams_index');
    }
}
