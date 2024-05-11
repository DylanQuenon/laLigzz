<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\User;
use App\Form\TeamType;
use Doctrine\ORM\EntityManager;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    #[Route('/admin/users/{page<\d+>?1}', name: 'admin_users_index')]
    public function index(PaginationService $pagination, int $page): Response
    {
        $pagination->setEntityClass(User::class) // App\Entity\Team string
                ->setPage($page)
                ->setLimit(10);
       
        
       

         return $this->render('admin/user/index.html.twig', [
           'pagination' => $pagination,
          
        ]);
    }
    #[Route("/admin/users/{id}/edit", name: "admin_users_edit")]
    public function edit(User $user, Request $request, EntityManagerInterface $manager): Response
    {
        
        $form = $this->createFormBuilder($user)
        ->add('roles', ChoiceType::class, [
            'choices' => [
                'Utilisateur' => 'ROLE_USER',
                'Administrateur' => 'ROLE_ADMIN',
                'Rédacteur'=> 'ROLE_REDACTEUR'
            ],
            'multiple' => true,
            'expanded' => true
        ])
        ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le roles de <strong>".$user->getFullName()."</strong> ont bien été modifiés"
            );
            return $this->redirectToRoute('admin_users_index',[
              
              ]);
            
        }

        return $this->render("admin/user/edit.html.twig",[
            "user" => $user,
            "myForm" => $form->createView()
        ]);

    }
 

}
