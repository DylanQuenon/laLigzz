<?php

namespace App\Controller;

use App\Entity\News;
use App\Form\SearchTeamType;
use App\Repository\NewsRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminNewsController extends AbstractController
{
   /**
     * Récupère les actus dans l'admin
     *
     * @param PaginationService $pagination
     * @param integer $page
     * @return Response
     */
    #[Route('/admin/news/{page<\d+>?1}', name: 'admin_news_index')]
    public function index(Request $request, PaginationService $pagination, NewsRepository $newsRepository, int $page): Response
    {
        $form = $this->createForm(SearchTeamType::class);
        $form->handleRequest($request);
        $isSubmitted = false;
    
        if ($form->isSubmitted() && $form->isValid()) {
            $term = $form->get('query')->getData();
            $isSubmitted = true;
            $news = $newsRepository->searchNewsByName($term);
        } else {
            $pagination->setEntityClass(News::class)
                       ->setPage($page)
                       ->setLimit(9);
    
            $news = $pagination->getData();
        }
    
        return $this->render('admin/news/index.html.twig', [
            'pagination' => $pagination,
            'news' => $news,
            'searchForm' => $form->createView(),
            'isSubmitted' => $isSubmitted,
        ]);
    }
    
       /**
     * Efface les news
     *
     * @param News $news
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/admin/news/{slug}/delete", name: "admin_news_delete")]
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
        
        return $this->redirectToRoute('admin_news_index');
    }
}
