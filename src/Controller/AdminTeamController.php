<?php

namespace App\Controller;

use App\Repository\TeamRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminTeamController extends AbstractController
{
    #[Route('/admin/teams', name: 'admin_teams_index')]
    public function index( TeamRepository $repo): Response
    {
        return $this->render('admin/team/index.html.twig', [
            'teams' => $repo->findAll(),
        ]);
    }
}
