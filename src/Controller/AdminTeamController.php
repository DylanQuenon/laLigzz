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
    /**
     * Récupère les équipes pour l'administration
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
<<<<<<< HEAD
            // dd($form['images']);
            $file = $form['logo']->getData();
            if(!empty($file))
            {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename."-".uniqid().'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                }catch(FileException $e)
                {
                    return $e->getMessage();
                }
                $team->setLogo($newFilename);

=======
              // gestion de l'image
              $file = $form['logo']->getData();
              if(!empty($file))
              {
                  $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                  $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                  $newFilename = $safeFilename."-".uniqid().'.'.$file->guessExtension();
                  try{
                      $file->move(
                          $this->getParameter('uploads_directory'),
                          $newFilename
                      );
                  }catch(FileException $e)
                  {
                      return $e->getMessage();
                  }
                  $team->setLogo($newFilename);
  
              }
            // gestion des images 
            foreach($team->getImages() as $image)
            {
                $file = $image->getFile();
                if(!empty($file))
                {
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                    $newFilename = $safeFilename."-".uniqid().'.'.$file->guessExtension();
                    try{
                        $file->move(
                            $this->getParameter('uploads_directory'),
                            $newFilename
                        );
                    }catch(FileException $e)
                    {
                        return $e->getMessage();
                    }
                    $image->setPath($newFilename);
    
                }
                $image->setTeam($team);
                $manager->persist($image);
>>>>>>> ffe2fa776d69e48fbc735910e40dec5c3170fe99
            }

            // gestion des images 
            // $files=->getData();
            // dd($files);
            foreach($form['images'] as $myFile){

                // dd($file->getUrl());
                $fileInfo=$myFile->getData();
                dd($myFile);
                $fileUrl=$fileInfo->getUrl();
                // dd($file->getUrl());

                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename."-".uniqid().'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                }catch(FileException $e)
                {
                    return $e->getMessage();
                }
                $image->setTeam($team);
                $manager->persist($image);

            }
            // je persiste mon objet team
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
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($team);
            $manager->flush();
            

            $this->addFlash(
                'success',
                "L'équipe <strong>".$team->getName()."</strong> a bien été modifiée"
            );
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
        $this->addFlash(
            "success",
            "L'annonce <strong>".$team->getName()."</strong> a bien été supprimée"
        );
        $manager->remove($team);
        $manager->flush();
        
        return $this->redirectToRoute('admin_teams_index');
    }
}
