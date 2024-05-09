<?php

namespace App\Controller;

use App\Repository\TeamRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(TeamRepository $team): Response
    {
        $teams = $team->findAll();
        return $this->render('home.html.twig', [
            'teams' => $teams
            
        ]);
    }
}
