<?php

namespace App\Controller;

use App\Entity\News;
use App\Form\NewsType;
use App\Form\NewsEditType;
use App\Entity\UserImgModify;
use App\Form\ImgUserModifyType;
use App\Repository\NewsRepository;
use App\Service\PaginationService;
use App\Service\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NewsController extends AbstractController
{
    /**
     * Affiche toutes les news
     *
     * @param NewsRepository $repo
     * @param PaginationService $pagination
     * @param integer $page
     * @return Response
     */
    #[Route('/news/{page<\d+>?1}', name: 'news_index')]
    public function index(NewsRepository $repo,PaginationService $pagination, int $page): Response
    {
        $limit = 9;

        $pagination->setEntityClass(News::class)
        ->setPage($page)
        ->setLimit($limit);
        $totalTeams = $repo->count([]);
    
        // Vérifie si le nombre total d'équipes et la limite sont non nuls avant de calculer le nombre de pages
        $totalPages = ($totalTeams > 0 && $limit > 0) ? ceil($totalTeams / $limit) : 1;
        if ($page < 1 || $page > $totalPages) {
            // Redirige vers la dernière page
            return $this->redirectToRoute('team_index', ['page' => $totalPages]);
        }
        return $this->render('news/index.html.twig', [
            'pagination' => $pagination
        ]);
    }
    
    /**
     * Ajoute une actualité
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param FileUploaderService $fileUploader
     * @return Response
     */
    #[Route("/news/add", name:"news_create")]
    public function create(Request $request, EntityManagerInterface $manager, FileUploaderService $fileUploader): Response
    {
        $news = new News();
        $form = $this->createForm(NewsType::class, $news);     
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $file = $form['cover']->getData();
            if($file){
                $imageName = $fileUploader->upload($file);
                $news->setCover($imageName);
            }
            $news->setAuthor($this->getUser());
            // je persiste mon objet team
            $manager->persist($news);
            // j'envoie les persistances dans la bdd
            $manager->flush();

            $this->addFlash(
                'success', 
                "L'actualité <strong>".$news->getTitle()."</strong> a bien été enregistrée"
            );

            return $this->redirectToRoute('news_show',[
                'slug' => $news->getSlug()
            ]);
        }

        return $this->render("news/add.html.twig",[
            'myForm' => $form->createView()
        ]);
    }
  
    #[Route("/news/{slug}/edit", name: "news_edit")]
    public function edit(News $news, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(NewsEditType::class, $news);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($news);
            $manager->flush();
    
            $this->addFlash(
                'success',
                "L'actualité <strong>".$news->getTitle()."</strong> a bien été modifiée"
            );
    
            return $this->redirectToRoute('news_show',[
                'slug' => $news->getSlug()
            ]);
        }
    
        return $this->render("news/edit.html.twig",[
            "news" => $news,
            "myForm" => $form->createView()
        ]);
    }
        #[Route("/news/{slug}/imgmodify", name:"news_img")]
        public function imgModify(Request $request, EntityManagerInterface $manager, News $news): Response
        {
            $imgModify = new UserImgModify();
            $form = $this->createForm(ImgUserModifyType::class, $imgModify);
            $form->handleRequest($request);
    
            if($form->isSubmitted() && $form->isValid())
            {
             
                if(!empty($news->getCover()))
                {
                    unlink($this->getParameter('uploads_directory').'/'.$news->getCover());
                }
    
                  // gestion de l'image
                  $file = $form['newPicture']->getData();
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
                      $news->setCover($newFilename);
                  }
                  $manager->persist($news);
                  $manager->flush();
    
                  $this->addFlash(
                    'success',
                    'La couverture a bien été modifié'
                  );
    
                  return $this->redirectToRoute('news_edit',[
                    'slug' => $news->getSlug()
                ]);
            }
    
            return $this->render("news/imgModify.html.twig",[
                'myForm' => $form->createView()
            ]);
        }

    /**
     * Vue individuelle des news
     *
     * @param string $slug
     * @param News $news
     * @return Response
     */
    #[Route("/news/{slug}", name:"news_show")]
    public function show(string $slug, News $news): Response
    {
        return $this->render("news/show.html.twig", [
            'news' => $news,
        ]);
    }

}
