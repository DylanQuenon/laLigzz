<?php

namespace App\DataFixtures;
use Faker\Factory;

use App\Entity\Team;
use App\Entity\User;
use App\Entity\Image;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture

{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // création d'un admin
        $admin = new User();
        $admin->setFirstName('Dylan')
              ->setLastName('Quenon')
              ->setEmail('dylan@epse.be')
              ->setPassword($this->passwordHasher->hashPassword($admin, 'password'))
              ->setIntroduction($faker->sentence())
              ->setDescription('<p>'.join('</p><p>',$faker->paragraphs(3)).'</p>')
              ->setRoles(['ROLE_ADMIN'])
              ->setPicture('');
          
        $manager->persist($admin);

        // gestion des utilisateurs 
        $users = []; // init d'un tableau pour récup des user pour les annonces
        $genres = ['male','femelle'];

        for($u=1 ; $u <= 10; $u++)
        {
            $user = new User();
            $genre = $faker->randomElement($genres);

            $hash = $this->passwordHasher->hashPassword($user, 'password');

            $user->setFirstName($faker->firstName($genre))
                 ->setLastName($faker->lastName())
                 ->setEmail($faker->email())
                 ->setIntroduction($faker->sentence())
                 ->setDescription('<p>'.join('</p><p>',$faker->paragraphs(3)).'</p>')
                 ->setPassword($hash)
                 ->setPicture('');

            $manager->persist($user);    

            $users[] = $user; 
        }

        //fixtures des équipes
        $captions = ['logo_bg', 'cover', 'news_image'];
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
            
            // Initialisation d'un tableau pour stocker les types de caption
            $teamCaptions = [];

            // Gestion des images
            for($g=1; $g <= 3; $g++)
            {
                $image = new Image();
                $image->setPath('https://picsum.photos/id/'.$g.'/900');

                // Sélection aléatoire d'un type de caption qui n'a pas déjà été utilisé
                do {
                    $captionType = $captions[array_rand($captions)];
                } while (in_array($captionType, $teamCaptions));

                // Ajout du type de caption utilisé dans le tableau des captions de l'équipe
                $teamCaptions[] = $captionType;

                $image->setCaption($captionType)
                      ->setTeam($team);
                
                $manager->persist($image);    
            }
            
            $manager->persist($team);
        }

        $manager->flush();
    }
}
