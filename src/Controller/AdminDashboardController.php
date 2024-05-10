<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{

    #[Route('/admin', name: 'admin_dashboard_index')]
    public function index(EntityManagerInterface $manager): Response
    {
        $users = $manager->createQuery("SELECT COUNT(u) FROM App\Entity\User u")->getSingleScalarResult(); // pour récup une valeur et pas un tableau
        $teams = $manager->createQuery("SELECT COUNT(a) FROM App\Entity\Team a")->getSingleScalarResult();
    

        // $val1 = "test";
        // $val2 = "test";
        // $val3 = "test";

        // $tab = [$val1, $val2, $val3]; 
        // ["test","test","test"]; 
        // $tab = compact("val1","val2","val3");
        // [
        //     "val1" => "test",
        //     "val2" => "test",
        //     "val3" => "test"
        // ];
        // $stats = compact('users','ads','bookings','comments');
        // 'stats' => compact('users','ads','bookings','comments');
        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => [
                'users' => $users,
                'teams' => $teams,
          
            ],
         
        ]);
    }
}