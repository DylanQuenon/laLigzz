<?php

namespace App\Controller;

use App\Repository\NewsRepository;
use App\Repository\TeamRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(TeamRepository $team, NewsRepository $new): Response
    {
        $teams = $team->findAll();
        $latestNews = $new->findBy([], ['id' => 'DESC'], 10);
        return $this->render('home.html.twig', [
            'teams' => $teams,
            'news'=>$latestNews
            
        ]);
    }
}
