<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\ImgModifyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminModifyImageController extends AbstractController
{
    /**
     * Permet de modifier l'image par rapport à son type
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Team $team
     * @return Response
     */
    #[Route("/admin/teams/{slug}/imgModify", name:"TeamImgModify")]
    public function imgModify(Request $request, EntityManagerInterface $manager, Team $team): Response
    {
        
        $type = $request->query->get('type');
        switch ($type) {
            case 'logo':
                $form = $this->createForm(ImgModifyType::class, null, ['image_type' => 'logo']);
                break;
            case 'logoBackground':
                $form = $this->createForm(ImgModifyType::class, null, ['image_type' => 'logoBackground']);
                break;
            case 'cover':
                $form = $this->createForm(ImgModifyType::class, null, ['image_type' => 'cover']);
                break;
            case 'newsPicture':
                $form = $this->createForm(ImgModifyType::class, null, ['image_type' => 'newsPicture']);
                break;
            default:
                throw $this->createNotFoundException('Type d\'image non valide.');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $file = $data['newPicture'];

            if (!empty($file)) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    return $e->getMessage();
                }

                // Modifie l'image appropriée en fonction du type d'image sélectionné
                switch ($type) {
                    case 'logo':
                        $team->setLogo($newFilename);
                        break;
                    case 'logoBackground':
                        $team->setLogoBackground($newFilename);
                        break;
                    case 'cover':
                        $team->setCover($newFilename);
                        break;
                    case 'newsPicture':
                        $team->setNewsPicture($newFilename);
                        break;
                }

                $manager->persist($team);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'L\'image a bien été modifiée'
                );

                return $this->redirectToRoute('admin_teams_index');
            }
        }

        return $this->render("admin/team/imgModify.html.twig",[
            'myForm' => $form->createView(),
            'teamName' => $team->getName(),
            'imageType' => $type,
            'oldImagePath' => $this->getOldImagePath($team, $type), // Ajoutez cette ligne pour passer le chemin de l'ancienne image
        ]);
        
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

}
