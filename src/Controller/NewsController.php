<?php

namespace App\Controller;

use App\Entity\News;
use App\Form\NewsType;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\NewsEditType;
use App\Entity\UserImgModify;
use App\Form\ImgUserModifyType;
use App\Repository\NewsRepository;
use App\Service\PaginationService;
use App\Service\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NewsController extends AbstractController
{

    #[Route('/news/search/ajax', name: 'news_search_ajax', methods: ['GET'])]
    /**
     * Effectue la recherche
     *
     * @param Request $request
     * @param NewsRepository $newsRepo
     * @return JsonResponse
     */
    public function searchAjax(Request $request, NewsRepository $newsRepo): JsonResponse
    {
        $query = $request->query->get('query', '');

        if (empty($query)) {
            return new JsonResponse([]); // Renvoie un tableau vide si aucun terme
        }

        $results = $newsRepo->findByNewsTitle($query)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        $jsonResults = array_map(function ($news) {
            return [
                'title' => $news->getTitle(),
                'author' => $news->getAuthor()->getFullName(),
                'slug' => $news->getSlug(),
            ];
        }, $results);

        return new JsonResponse($jsonResults);
    }
    /**
     * Affiche toutes les news
     *
     * @param NewsRepository $repo
     * @param PaginationService $pagination
     * @param integer $page
     * @return Response
     */
    #[Route('/news/{page<\d+>?1}', name: 'news_index')]
    public function index(NewsRepository $repo, PaginationService $pagination, int $page): Response
    {

        $pagination->setEntityClass(News::class)
            ->setPage($page)
            ->setLimit(9)
            ->setOrder(['createdAt' => 'DESC']); // Trier par date de création décroissante

        $totalNews = $repo->count([]);

        // Calculer le nombre total de pages
        $totalPages = ($totalNews > 0) ? ceil($totalNews / 9) : 1;

        // Vérifier si la page demandée est valide
        if ($page < 1 || $page > $totalPages) {
            // Rediriger vers la première page
            return $this->redirectToRoute('news_index', ['page' => 1]);
        }

        return $this->render('news/index.html.twig', [
            'news' => $pagination->getData(),
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
    #[IsGranted(
        attribute: new Expression('(is_granted("ROLE_REDACTEUR")) or is_granted("ROLE_ADMIN")'),
        message: "Vous n'avez pas l'autorisation de poster des actualités"
    )]
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
  
    /**
     * Modifier une news
     *
     * @param News $news
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/news/{slug}/edit", name: "news_edit")]
    #[IsGranted(
        attribute: new Expression('(user === subject and is_granted("ROLE_USER")) or is_granted("ROLE_ADMIN") or is_granted("ROLE_REDACTEUR")'),
        subject: new Expression('args["news"].getAuthor()'),
        message: "Cette actualité ne vous appartient pas, vous ne pouvez pas la modifier"
    )]
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
    /**
     * Modifier la cover de l'image
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param News $news
     * @param FileUploaderService $fileUploader
     * @return Response
     */
    #[Route("/news/{slug}/imgmodify", name:"news_img")]
    public function imgModify(Request $request, EntityManagerInterface $manager, News $news, FileUploaderService $fileUploader): Response
    {
        $imgModify = new UserImgModify();
        $form = $this->createForm(ImgUserModifyType::class, $imgModify);
        $form->handleRequest($request);
        
        

        if($form->isSubmitted() && $form->isValid())
        {
            
            if(!$news->getCover() || !empty($news->getCover()))
            {
                unlink($this->getParameter('uploads_directory').'/'.$news->getCover());
            }

                // gestion de l'image
                $file = $form['newPicture']->getData();
                if($file){
                    $imageName = $fileUploader->upload($file);
                    $news->setCover($imageName);
                }
                $manager->persist($news);
                $manager->flush();

                $this->addFlash(
                'success',
                'La couverture a bien été modifiée'
                );

                return $this->redirectToRoute('news_show',[
                'slug' => $news->getSlug()
                
            ]);
        }

        return $this->render("news/imgModify.html.twig",[
            'myForm' => $form->createView(),
            
        'news' => $news 
            
        ]);
    }
    /**
     * Efface les news
     *
     * @param News $news
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/news/{slug}/delete", name: "news_delete")]
    #[IsGranted(
        attribute: new Expression('(user === subject and is_granted("ROLE_USER")) or is_granted("ROLE_ADMIN")'),
        subject: new Expression('args["news"].getAuthor()'),
        message: "Cette annonce ne vous appartient pas, vous ne pouvez pas la supprimer"
    )]
    public function delete(News $news, EntityManagerInterface $manager): Response
    {
        if(!empty($news->getCover()))
        {
            unlink($this->getParameter('uploads_directory').'/'.$news->getCover());
        }
      
        $this->addFlash(
            "success",
            "L'annonce <strong>".$news->getTitle()."</strong> a bien été supprimée"
        );
        $manager->remove($news);
        $manager->flush();
        
        return $this->redirectToRoute('news_index');
    }
 
    /**
     * Vue individuelle des news
     *
     * @param string $slug
     * @param News $news
     * @return Response
     */
    #[Route("/news/{slug}", name:"news_show")]
    public function show(string $slug, News $news, NewsRepository $newsRepository, Request $request,EntityManagerInterface $manager ): Response
    {
        $comment = new Comment(); //créé un nouveau commentaire
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifie si l'utilisateur essaie de commenter sa propre news
            if ($this->getUser() === $news->getAuthor()) {
                $this->addFlash(
                    'warning',
                    'Vous pouvez pas commenter votre propre actualité'
                );
                return $this->redirectToRoute('news_show', ['slug' => $news->getSlug()]);
            }
    
    
            $comment->setNews($news) //récupère l'actualité  commentée
                    ->setAuthor($this->getUser());//récupère l'auteur qui a commenté
    
            // Persiste le commentaire
            $manager->persist($comment);
            $manager->flush();
    
            $this->addFlash(
                'success',
                "Votre commentaire a été pris en compte"
            );
    
            // Redirige vers la même page pour réinitialiser le formulaire
            return $this->redirectToRoute('news_show', ['slug' => $news->getSlug()]);
        }
     
    
        $previousNews = $newsRepository->findPreviousNews($news->getId());
        $nextNews = $newsRepository->findNextNews($news->getId());
    
        return $this->render("news/show.html.twig", [
            'news' => $news,
            'previousNews' => $previousNews,
            'nextNews' => $nextNews,
            'myForm' => $form->createView(),
        ]);
    }

    #[Route("/comment/{id}/delete", name: "comment_delete")]
    #[IsGranted(
        attribute: new Expression('(user === subject and is_granted("ROLE_USER")) or is_granted("ROLE_ADMIN") or is_granted("ROLE_MODERATEUR")'),
        subject: new Expression('args["comment"].getAuthor()'),
        message: "Le commentaire ne vous appartient pas, vous ne pouvez pas l'effacer"
    )]
    public function deleteComment(Comment $comment, EntityManagerInterface $manager): Response
    {
        // Vérifier si l'utilisateur connecté est l'auteur du commentaire
        $news = $comment->getNews();
        $manager->remove($comment);
        $manager->flush();
    
        $this->addFlash('success', "Le commentaire a été effacé avec succès");

        return $this->redirectToRoute('news_show', ['slug' => $news->getSlug()]);
    }

 

    
}
