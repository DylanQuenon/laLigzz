<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    #[Route('/admin/comments/{page<\d+>?1}', name: 'admin_comments_index')]
    public function index(int $page, PaginationService $pagination): Response
    {
        $pagination->setEntityClass(Comment::class)
                ->setPage($page)
                ->setLimit(15);

        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $pagination
        ]);
    }


    /**
     * Permet de supprimer un commentaire
     *
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/admin/comments/{id}/delete", name:"admin_comments_delete")]
    public function delete(Comment $comment, EntityManagerInterface $manager): Response
    {
        $this->addFlash(
            "success",
            "Le commentaire n°".$comment->getId()." a bien été supprimé"
        );

        $manager->remove($comment);
        $manager->flush();

        return $this->redirectToRoute("admin_comments_index");
    }
}
