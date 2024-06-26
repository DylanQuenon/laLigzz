<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\DeleteType;
use App\Form\AccountType;
use App\Entity\UserImgModify;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\ImgUserModifyType;
use App\Form\PasswordUpdateType;
use App\Service\FileUploaderService;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\TooManyLoginAttemptsAuthenticationException;

class AccountController extends AbstractController
{
 
    /**
     * Permet de se connecter à son compte
     *
     * @param AuthenticationUtils $utils
     * @return Response
     */
    #[Route('/login', name: 'account_login')]
    public function index(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        $loginError = null;

        
        if($error instanceof TooManyLoginAttemptsAuthenticationException)
        {
            // l'ereur est due à la limitation de tentative de connexion
            $loginError = "Trop de tentatives de connexion. Réessayez plus tard";
        }
        return $this->render('account/index.html.twig', [
            'hasError' => $error !== null,
            'username' => $username,
            'loginError' => $loginError
        ]);
    }
     /**
     * Permet de se déconnecter
     *
     * @return void
     */
    #[Route("/logout", name: "account_logout")]
    public function logout(): void
    {

    }
           /**
     * Permet d'afficher le formulaire d'inscription ainsi la gestion de l'inscription de l'utilisateur
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Route("/register", name:"account_register")]
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher,FileUploaderService $fileUploader): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        // partie traitement du formulaire
        if($form->isSubmitted() && $form->isValid())
        {

            // gestion de l'image
            $file = $form['picture']->getData();
            if($file){
                $imageName = $fileUploader->upload($file);
                $user->setPicture($imageName);
            }

            // gestion de l'inscription dans la bdd
            $hash = $hasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();


            return $this->redirectToRoute('account_login');
        }


        return $this->render("account/registration.html.twig",[
            'myForm' => $form->createView()
        ]);
    }
    /**
     * Permet d'éditer son profil
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/account/profile", name:"account_profile")]
    public function profile(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser(); // permet de récup l'utilisateur connecté

        
        $fileName = $user->getPicture();
        if(!empty($fileName)){
            $user->setPicture(
                new File($this->getParameter('uploads_directory').'/'.$user->getPicture())
            );
        }

        $form = $this->createForm(AccountType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $user->setSlug('')
                ->setPicture($fileName);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les données ont été enregistrées avec succés"
            );

            return $this->redirectToRoute('account_index');
        }

        return $this->render("account/profile.html.twig",[
            'myForm' => $form->createView()
        ]);

    }

     /**
     * Permet de modifier le mot de passe
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Route("/account/password-update", name:"account_password")]
    #[IsGranted('ROLE_USER')]
    public function updatePassword(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $passwordUpdate = new PasswordUpdate();
        $user = $this->getUser();
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // vérifier si le mot de passe correspond à l'ancien
            if(!password_verify($passwordUpdate->getOldPassword(),$user->getPassword()))
            {
                // gestion de l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel"));
            }else{
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $hasher->hashPassword($user, $newPassword);

                $user->setPassword($hash);
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre mot de passe a bien été modifié'
                );

                return $this->redirectToRoute('account_index');
            }

        }

        return $this->render("account/password.html.twig", [
            'myForm' => $form->createView()
        ]);

    }
    #[Route("/account/delimg", name:"account_delimg")]
    #[IsGranted('ROLE_USER')]
    public function removeImg(EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        if(!empty($user->getPicture()))
        {
            unlink($this->getParameter('uploads_directory').'/'.$user->getPicture());
            $user->setPicture('');
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre avatar a bien été supprimé'
            );
        }

        return $this->redirectToRoute('homepage');

    }
    #[Route("/account/imgmodify", name:"account_modifimg")]
    #[IsGranted('ROLE_USER')]
    public function imgModify(Request $request, EntityManagerInterface $manager): Response
    {
        $imgModify = new UserImgModify();
        $user = $this->getUser();
        $form = $this->createForm(ImgUserModifyType::class, $imgModify);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //permet de supprimer l'image dans le dossier
            // gestion de la non-obligation de l'image
            if(!empty($user->getPicture()))
            {
                unlink($this->getParameter('uploads_directory').'/'.$user->getPicture());
            }

              // gestion de l'image
              $file = $form['newPicture']->getData();
              if(!empty($file))
              {
                  $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                  $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                  $newFilename = $safeFilename."-".uniqid().'.'.$file->guessExtension();
                  try{
                      $file->move(
                          $this->getParameter('uploads_directory'),
                          $newFilename
                      );
                  }catch(FileException $e)
                  {
                      return $e->getMessage();
                  }
                  $user->setPicture($newFilename);
              }
              $manager->persist($user);
              $manager->flush();

              $this->addFlash(
                'success',
                'Votre avatar a bien été modifié'
              );

              return $this->redirectToRoute('account_index');

        }

        return $this->render("account/imgModify.html.twig",[
            'myForm' => $form->createView()
        ]);
    }

    #[Route("/account/followed-news", name:"account_followed_news")]
    #[IsGranted('ROLE_USER')]
    public function followedNews(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        $followedTeams = $user->getFollowedTeams();
        $followedNews = [];
        $displayedNews = []; // Tableau pour suivre les actualités déjà affichées
    
        foreach($followedTeams as $followedTeam)
        {
            foreach($followedTeam->getNews() as $news)
            {
                // Vérifier si l'actualité a déjà été affichée
                $newsId = $news->getId();
                if (!isset($displayedNews[$newsId])) {
                    $followedNews[] = $news;
                    // Marquer l'actualité comme déjà affichée
                    $displayedNews[$newsId] = true;
                }
            }
        }
            // Trier les actualités du plus récent au plus ancien
        usort($followedNews, function($a, $b) {
            return $b->getCreatedAt()->getTimestamp() - $a->getCreatedAt()->getTimestamp();
        });
    
        return $this->render("account/followedNews.html.twig", [
            'followedNews' => $followedNews,
            'user' => $user
        ]);
    }
    
    #[Route("/account/followed-matches", name: "account_followed_matches")]
    #[IsGranted('ROLE_USER')]
    public function followedMatches(EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        $followedTeams = $user->getFollowedTeams();
        $followedMatches = [];
        $displayedMatches = []; // Tableau pour suivre les matchs déjà affichés
    
        foreach ($followedTeams as $followedTeam) {
            foreach ($followedTeam->getMatches() as $match) {
                // Récupérer les équipes du match
                $homeTeam = $match->getHomeTeam();
                $awayTeam = $match->getAwayTeam();
                
                // Identifier les IDs des deux équipes
                $homeTeamId = $homeTeam->getId();
                $awayTeamId = $awayTeam->getId();
                
                // Vérifier si le match a déjà été affiché pour les deux équipes
                if (!isset($displayedMatches[$match->getId()][$homeTeamId]) &&
                    !isset($displayedMatches[$match->getId()][$awayTeamId])) {
                    
                    // Ajouter le match à la liste des matchs suivis
                    $followedMatches[] = $match;
                    
                    // Marquer le match comme déjà affiché pour les deux équipes
                    $displayedMatches[$match->getId()][$homeTeamId] = true;
                    $displayedMatches[$match->getId()][$awayTeamId] = true;
                }
            }
    
            foreach ($followedTeam->getAwayMatches() as $match) {
                // Récupérer les équipes du match
                $homeTeam = $match->getHomeTeam();
                $awayTeam = $match->getAwayTeam();
                
                // Identifier les IDs des deux équipes
                $homeTeamId = $homeTeam->getId();
                $awayTeamId = $awayTeam->getId();
                
                // Vérifier si le match a déjà été affiché pour les deux équipes
                if (!isset($displayedMatches[$match->getId()][$homeTeamId]) &&
                    !isset($displayedMatches[$match->getId()][$awayTeamId])) {
                    
                    // Ajouter le match à la liste des matchs suivis
                    $followedMatches[] = $match;
                    
                    // Marquer le match comme déjà affiché pour les deux équipes
                    $displayedMatches[$match->getId()][$homeTeamId] = true;
                    $displayedMatches[$match->getId()][$awayTeamId] = true;
                }
            }
        }
    
        // Trier les matches du plus récent au plus ancien
        usort($followedMatches, function($a, $b) {
            return $b->getDate()->getTimestamp() - $a->getDate()->getTimestamp();
        });
    
        return $this->render("account/followedMatches.html.twig", [
            'followedMatches' => $followedMatches,
            'user' => $user
        ]);
    }
    

     /**
     * Delete account
     *
     * @param Request $request
     * @param UserPasswordHasherInterface $hasher
     * @param EntityManagerInterface $manager
     * @param TokenStorageInterface $tokenStorage
     * @return Response
     */
    #[Route("/account/delete", name: "account_delete")]
    #[IsGranted('ROLE_USER')]
    public function deleteAccount(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $manager, TokenStorageInterface $tokenStorage): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(DeleteType::class);

        //if user not logged in -> redirect to login page
        if (!$user) {

            $this->addFlash(
                'danger',
                'Vous devez être connecté.'
            );
            return $this->redirectToRoute('account_login');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $submittedEmail = $data['email'];
            $submittedPassword = $data['password'];

            //email address in db?
            if ($user->getEmail() === $submittedEmail) {
                $isPasswordValid = $hasher->isPasswordValid($user, $submittedPassword);

                //password verify
                if ($isPasswordValid) {

                    $avatarFilename = $user->getPicture();

                    //avatar deleted
                    if ($avatarFilename) {
                        $avatarFilePath = $this->getParameter('uploads_directory') . '/' . $avatarFilename;
                        if (file_exists($avatarFilePath)) {
                            unlink($avatarFilePath);
                        }
                    }

                    //set connexion token to null
                    $tokenStorage->setToken(null);
                    //remove
                    $manager->remove($user);
                    $manager->flush();

                    $this->addFlash(
                        'success',
                        'Compte supprimé avec succès'
                    );

                    return $this->redirectToRoute('homepage');
                }
            }

            $this->addFlash(
                'danger',
                'Email ou mot de passe incorrect'
            );
        }

        return $this->render('account/delete.html.twig', [
            'myForm' => $form->createView()
        ]);
    }
    


}
