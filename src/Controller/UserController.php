<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\User;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * Affiche le profil d'un utilisateur
     *
     * @param User $user
     * @return Response
     */
    #[Route('/user/{slug}', name: 'user_show')]
    public function index(User $user): Response
    {
        $followedTeams=$user->getFollowedTeams();
        return $this->render('user/index.html.twig', [
            'user' => $user,
            'teamsFollow'=>$followedTeams
        ]);
    }

    #[Route('/account/teams/{slug}/remove-team-from-followed', name: 'remove_team_from_followed_user')]
    #[IsGranted('ROLE_USER')]
    public function RemoveTeamFromFollowed(Request $request, EntityManagerInterface $manager, Team $team, TeamRepository $teamRepository): Response
    {
        $user = $this->getUser();
        $team = $request->get('slug');
        if ($team) {
            $team = $teamRepository->findOneBy(['slug' => $team]);
            $user->removeFollowedTeam($team);
            $manager->persist($user);
            $manager->flush();
        }
        return $this->redirectToRoute('account_index');
    }

    /**
     * Permet d'afficher le profil de l'utilisateur connecté
     *
     * @return Response
     */
    #[Route("/account", name:"account_index")]
    #[IsGranted('ROLE_USER')]
    public function myAccount(): Response
    {
        $followedTeams=$this->getUser()->getFollowedTeams();
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser(),
            'teamsFollow'=>$followedTeams
        ]);
    }
}