<?php

namespace App\Controller;

use App\Service\StatsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{

    /**
     * Fonction pour aller sur le dashboard
     *
     * @param StatsService $statsService
     * @return Response
     */
    #[Route('/admin', name: 'admin_dashboard_index')]
    public function index(StatsService $statsService): Response
    {
        $users = $statsService->getUsersCount();
        $teams = $statsService->getTeamsCount();
        $news = $statsService->getNewsCount();
        $matches = $statsService->getAllGames();
       
        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => [
                'users' => $users,
                'teams' => $teams,
                'news'=>$news,
                'matches'=>$matches,
          
            ],
         
        ]);
    }
}