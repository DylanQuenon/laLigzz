<?php

namespace App\Form;

use App\Entity\Image;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ImageType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'attr' => [
                    'placeholder' => 'Url de l\'image'
                ]
            ])
            ->add('caption', ChoiceType::class, [
                'attr' => [
                    'placeholder' => 'Titre de l\'image'
                ],
                'choices' => $this->getUniqueCaptions(),
            ]);

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
    private function getUniqueCaptions(): array
    {
        $repository = $this->entityManager->getRepository(Image::class);
        $captions = $repository->createQueryBuilder('i')
            ->select('DISTINCT i.caption')
            ->getQuery()
            ->getResult();

        // Formater les résultats pour les utiliser comme choix dans le champ de sélection
        $choices = [];
        foreach ($captions as $caption) {
            $choices[$caption['caption']] = $caption['caption'];
        }

        return $choices;
    }
}



