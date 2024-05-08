<?php

namespace App\DataFixtures;
use Faker\Factory;

use App\Entity\Team;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
  
        for($i=1; $i<=20; $i++)
        {
            $team= new Team();
            $team->setName('equipe ' . $i)
            ->setLogo('https://upload.wikimedia.org/wikipedia/en/thumb/5/56/Real_Madrid_CF.svg/1200px-Real_Madrid_CF.svg.png')
            ->setDescription("Le Real Madrid, symbole de légende et d'excellence, incarne la passion et le prestige dans l'histoire du football.")
            ->setDevise($faker->sentence(1))
            ->setFondation($faker->dateTimeBetween('-100 years','now'))
            ->setCoach($faker->name())
            ->setGoalscorer($faker->name().'('.rand(100,400).')')
            ->setPresident($faker->name());
            
            $manager->persist($team);
        }

        $manager->flush();
    }
}
