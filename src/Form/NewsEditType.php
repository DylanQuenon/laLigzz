<?php

namespace App\Form;

use App\Entity\News;
use App\Entity\Team;
use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class NewsEditType extends ApplicationType
{
 

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration('Titre', 'Entrez le titre de la news'))
            ->add('subtitle', TextType::class, $this->getConfiguration('Sous-titre', 'Entrez le sous-titre de la news'))
          
            // ->add('cover', FileType::class, $this->getConfiguration('Couverture', 'URL de l\'image de couverture'))
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Officiel' => 'officiel',
                    'Rumeur' => 'rumeur',
                ],
                'label' => 'Statut',
                'placeholder' => 'Entrez le statut de la news',
            ])
            ->add('team', EntityType::class, [
                'class' => Team::class,
                'choice_label' => 'name',
                'multiple' => true,
                'label' => 'Équipe',
                'placeholder' => 'Sélectionnez une ou plusieurs équipes',
            ])
            ->add('text', TextareaType::class, $this->getConfiguration('Texte', 'Entrez le contenu de la news'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}
