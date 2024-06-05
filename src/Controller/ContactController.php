<?php

// src/Controller/ContactController.php
namespace App\Controller;

use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $firstName = $request->request->get('firstName');
            $name = $request->request->get('name');
            $email = $request->request->get('mail');
            $message = $request->request->get('message');

            // Utilisation de renderView() pour obtenir une chaîne de caractères HTML
            $htmlContent = $this->renderView('email/contact_email.html.twig', [
                'firstName' => $firstName,
                'name' => $name,
                'email' => $email,
                'message' => $message,
            ]);

            $emailMessage = (new Email())
                ->from($email)
                ->to('dylan.quenon.04@gmail.com') // Remplacez par votre adresse email
                ->subject('Contact Form Submission')
                ->html($htmlContent);

            $mailer->send($emailMessage);

            $this->addFlash('success', 'Votre message a été envoyé avec succès.');
            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig');
    }
}
