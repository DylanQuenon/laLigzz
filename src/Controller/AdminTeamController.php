<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\Image;
use App\Form\TeamType;
use App\Entity\Matches;
use App\Entity\Ranking;
use App\Form\TeamEditType;
use App\Repository\TeamRepository;
use App\Service\PaginationService;
use App\Service\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminTeamController extends AbstractController
{
  
    /**
     * Récupère les équipes dans l'admin
     *
     * @param PaginationService $pagination
     * @param integer $page
     * @return Response
     */
    #[Route('/admin/teams/{page<\d+>?1}', name: 'admin_teams_index')]
    public function index(PaginationService $pagination, int $page): Response
    {
        $pagination->setEntityClass(Team::class) // App\Entity\Team string
                ->setPage($page)
                ->setLimit(9);
       

        return $this->render('admin/team/index.html.twig', [
           'pagination' => $pagination
        ]);
    }

    /**
     * Permet d'ajouter une équipe
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param FileUploaderService $fileUploader
     * @return Response
     */
    #[Route("/admin/teams/new", name:"admin_teams_create")]
    public function create(Request $request, EntityManagerInterface $manager, FileUploaderService $fileUploader): Response
    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);     
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->handleFileUpload($form, $team, 'logo', $fileUploader);
            $this->handleFileUpload($form, $team, 'logoBackground', $fileUploader);
            $this->handleFileUpload($form, $team, 'cover', $fileUploader);
            $this->handleFileUpload($form, $team, 'newsPicture', $fileUploader);

            // je persiste mon objet team
            $manager->persist($team);
            $manager->flush();
            // j'envoie les persistances dans la bdd
            $ranking=new Ranking();
            $ranking->setTeam($team)
            ->setMatchesPlayed(0)
            ->setWins(0)
            ->setDraws(0)
            ->setLosses(0)
            ->setGoalsFor(0)
            ->setGoalsAgainst(0)
            ->setPoints(0);
            $manager->persist($ranking);
            $manager->flush();
            

            $this->addFlash(
                'success', 
                "L'équipe du <strong>".$team->getName()."</strong> a bien été enregistrée"
            );

            return $this->redirectToRoute('admin_teams_edit',[
                'slug' => $team->getSlug()
            ]);
        }

        return $this->render("admin/team/new.html.twig",[
            'myForm' => $form->createView()
        ]);
    }
    /**
     * Permet d'optimiser pour l'upload
     *
     * @param [type] $form
     * @param [type] $team
     * @param [type] $field
     * @param [type] $fileUploader
     * @return void
     */
    private function handleFileUpload($form, $team, $field, $fileUploader)
    {
        $file = $form[$field]->getData();
        if ($file) {
            $newFilename = $fileUploader->upload($file);
            $setter = 'set' . ucfirst($field);
            $team->$setter($newFilename);
        }
    }


    /**
     * Permet de modifier une team
     *
     * @param Teams $team
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/admin/teams/{slug}/edit", name: "admin_teams_edit")]
    public function edit(Team $team, Request $request, EntityManagerInterface $manager): Response
    {
        $oldLogoPath = $this->getOldImagePath($team, 'logo');
        $oldLogoBackgroundPath = $this->getOldImagePath($team, 'logoBackground');
        $oldCoverPath = $this->getOldImagePath($team, 'cover');
        $oldNewsPicturePath = $this->getOldImagePath($team, 'newsPicture');
        
        $logo = $team->getLogo();
        if(!empty($logo)){
            $team->setLogo(
                new File($this->getParameter('uploads_directory').'/'.$team->getLogo())
            );
        }
        $logoBackground = $team->getLogoBackground();
        if(!empty($logoBackground)){
            $team->setLogoBackground(
                new File($this->getParameter('uploads_directory').'/'.$team->getLogoBackground())
            );
        }
        $cover = $team->getCover();
        if(!empty($cover)){
            $team->setCover(
                new File($this->getParameter('uploads_directory').'/'.$team->getCover())
            );
        }
        $newsPicture = $team->getNewsPicture();
        if(!empty($newsPicture)){
            $team->setNewsPicture(
                new File($this->getParameter('uploads_directory').'/'.$team->getNewsPicture())
            );
        }

     

        $form = $this->createForm(TeamEditType::class, $team);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $team->setLogo($logo)
                ->setSlug('')
                ->setLogoBackground($logoBackground)
                ->setCover($cover)
                ->setNewsPicture($newsPicture);

            $manager->persist($team);
            $manager->flush();
    
            $this->addFlash(
                'success',
                "L'équipe <strong>".$team->getName()."</strong> a bien été modifiée"
            );
    
            return $this->redirectToRoute('admin_teams_index');
        }
    
        return $this->render("admin/team/edit.html.twig",[
            "team" => $team,
            "myForm" => $form->createView(),
            "oldLogoPath" => $oldLogoPath,
            "oldLogoBackgroundPath" => $oldLogoBackgroundPath,
            "oldCoverPath" => $oldCoverPath,
            "oldNewsPicturePath" => $oldNewsPicturePath,
        ]);
    }
     
    /**
     * Permet d'effacer une team
     *
     * @param Team $team
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/admin/teams/{slug}/delete", name: "admin_teams_delete")]
    public function delete(Team $team, EntityManagerInterface $manager): Response
    {
        $this->deleteAssociatedMatches($team, $manager);
        if(!empty($team->getLogo()))
        {
            unlink($this->getParameter('uploads_directory').'/'.$team->getLogo());
        }
        if(!empty($team->getLogoBackground()))
        {
            unlink($this->getParameter('uploads_directory').'/'.$team->getLogoBackground());
        }
        if(!empty($team->getCover()))
        {
            unlink($this->getParameter('uploads_directory').'/'.$team->getCover());
        }
        if(!empty($team->getNewsPicture()))
        {
            unlink($this->getParameter('uploads_directory').'/'.$team->getNewsPicture());
        }
        $this->addFlash(
            "success",
            "L'annonce <strong>".$team->getName()."</strong> a bien été supprimée"
        );
        $manager->remove($team);
        $manager->flush();
        
        return $this->redirectToRoute('admin_teams_index');
    }

    private function getOldImagePath(Team $team, string $type): ?string
    {
        switch ($type) {
            case 'logo':
                return $team->getLogo();
            case 'logoBackground':
                return $team->getLogoBackground();
            case 'cover':
                return $team->getCover();
            case 'newsPicture':
                return $team->getNewsPicture();
            default:
                return null;
        }
    }

    private function deleteAssociatedMatches(Team $team, EntityManagerInterface $manager): void
{
    // Récupérer tous les matchs associés à l'équipe en tant qu'équipe à domicile ou à l'extérieur
    $matches = $manager->getRepository(Matches::class)->findBy(['homeTeam' => $team]);

    foreach ($matches as $match) {
        $this->deleteMatchAndUpdateRanking($match, $manager);
    }

    $matches = $manager->getRepository(Matches::class)->findBy(['awayTeam' => $team]);

    foreach ($matches as $match) {
        $this->deleteMatchAndUpdateRanking($match, $manager);
    }
}
    private function deleteMatchAndUpdateRanking(Matches $match, EntityManagerInterface $manager): void
{
    $homeTeamGoals = $match->getHomeTeamGoals();
    $awayTeamGoals = $match->getAwayTeamGoals();

    $this->cancelTeamRanking($match->getHomeTeam(), $homeTeamGoals, $awayTeamGoals, $manager);
    $this->cancelTeamRanking($match->getAwayTeam(), $awayTeamGoals, $homeTeamGoals, $manager);

    $manager->remove($match);
    $manager->flush();
}

private function cancelTeamRanking(Team $team, int $oldGoalsFor, int $oldGoalsAgainst, EntityManagerInterface $manager): void
{
    $ranking = $team->getRanking();

    $ranking->setMatchesPlayed($ranking->getMatchesPlayed() - 1);
    $ranking->setGoalsFor($ranking->getGoalsFor() - $oldGoalsFor);
    $ranking->setGoalsAgainst($ranking->getGoalsAgainst() - $oldGoalsAgainst);

    if ($oldGoalsFor > $oldGoalsAgainst) {
        $ranking->setWins($ranking->getWins() - 1);
        $ranking->setPoints($ranking->getPoints() - 3);
    } elseif ($oldGoalsFor < $oldGoalsAgainst) {
        $ranking->setLosses($ranking->getLosses() - 1);
    } else {
        $ranking->setDraws($ranking->getDraws() - 1);
        $ranking->setPoints($ranking->getPoints() - 1);
    }

    $manager->persist($ranking);
    $manager->flush();
}
}
