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
            ->setLogo('https://picsum.photos/id/'.$i.'/200/200')
            ->setDescription($faker->sentence(2))
            ->setDevise($faker->sentence(1))
            ->setFondation($faker->dateTimeBetween('-100 years','now'))
            ->setCoach($faker->name())
            ->setGoalscorer($faker->name())
            ->setPresident($faker->name());
            
            $manager->persist($team);
        }

        $manager->flush();
    }
}
