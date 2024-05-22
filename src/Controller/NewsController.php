<?php

namespace App\Controller;

use App\Entity\News;
use App\Form\NewsType;
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

    #[Route("/news/{slug}", name:"news_show")]
    public function show(string $slug, News $news): Response
    {
        

    
        return $this->render("news/show.html.twig", [
            'news' => $news,
        ]);
    }

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

            return $this->redirectToRoute('homepage',[
                'slug' => $news->getSlug()
            ]);
        }

        return $this->render("news/add.html.twig",[
            'myForm' => $form->createView()
        ]);
    }
}
