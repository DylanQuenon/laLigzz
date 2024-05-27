<?php

namespace App\Form;

use App\Entity\Team;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TeamType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Nom de l'équipe",
                'attr' => [
                    'placeholder' => "Entrez le nom de l'équipe",
                ]
                ])
            ->add('logo', FileType::class, $this->getConfiguration("Logo du club", "Choisissez le logo du club que vous insérez"))
            ->add('description',TextareaType::class, $this->getConfiguration('Description du club','Donnez une brève description du club'))
            ->add('stadium',TextType::class,$this->getConfiguration('Stade du club','Quel est le stade du club'))
            ->add('devise',TextType::class, $this->getConfiguration('Devise','Quelle est la devise du club'))
            ->add('fondation', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => "Entrez la date de fondation de l'équipe",
                ],
                'label'=>"Date de fondation de l'équipe",
            ])
            ->add('coach', TextType::class,$this->getConfiguration("Nom du coach","Qui est le coach actuel du club?"))
            ->add('goalscorer',TextType::class,$this->getConfiguration("Nom du meilleur buteur","Qui est le meilleur buteur du club?"))
            ->add('president',TextType::class,$this->getConfiguration("Nom du président","Qui est le président du club?"))
            ->add('logoBackground', FileType::class, $this->getConfiguration("Logo de l'image de fond en noir et blanc", "Choisir l'image"))
            ->add('cover', FileType::class, $this->getConfiguration("L'image qui se trouvera en couverture sur la page équipe", "Choisir l'image"))
            ->add('newsPicture', FileType::class, $this->getConfiguration("Image de la bannière news", "Choisir l'image"));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }
}
