<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\Matches;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class MatchesType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = [];
        for ($i = 1; $i <= 38; $i++) {
            $choices["Journée $i"] = $i;
        }
        $builder
            ->add('journee', ChoiceType::class, [
                'choices' => $choices,
                'label' => 'Journée',
                'placeholder' => 'Sélectionnez une journée'
            ])
            // ->add('stadium',TextType::class, $this->getConfiguration('Stade', 'Entrez le nom du stade'))
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                
            ])
            ->add('homeTeam', EntityType::class, [
                'class' => Team::class,
                'choice_label' => 'name',
                ])
            ->add('homeTeamGoals',IntegerType::class, $this->getConfiguration('Buts équipe domicile', 'Entrez le nombre de buts de l\'équipe domicile'))
            ->add('awayTeam', EntityType::class, [
                'class' => Team::class,
                'choice_label' => 'name',
                ])
            ->add('awayTeamGoals',IntegerType::class, $this->getConfiguration('Buts équipe extérieur', 'Entrez le nombre de buts de l\'équipe extérieure'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Matches::class,
        ]);
    }
}
