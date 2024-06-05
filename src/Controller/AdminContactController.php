<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminContactController extends AbstractController
{
    #[Route('/admin/contact/{id}', name: 'admin_contacts_show')]
    public function show(Contact $contact): Response
    {
        return $this->render('admin/contact/show.html.twig', [
            'contact' => $contact
        ]);
    }

    #[Route('/admin/contact/{page<\d+>?1}', name: 'admin_contact_index')]
    public function index(PaginationService $pagination, int $page): Response
    {
        $pagination->setEntityClass(Contact::class)
                ->setPage($page)
                ->setLimit(9);
       
        return $this->render('admin/contact/index.html.twig', [
           'pagination' => $pagination
        ]);
    }
    
    #[Route('/admin/contact/{id}/delete', name: 'admin_contacts_delete')]
    public function delete(Contact $contact, EntityManagerInterface $manager): Response
    {
        $this->addFlash(
            "success",
            "Le message numéro <strong>".$contact->getId()."</strong> a bien été supprimé"
        );
        $manager->remove($contact);
        $manager->flush();
        
        return $this->redirectToRoute('admin_contact_index');
    }
}
