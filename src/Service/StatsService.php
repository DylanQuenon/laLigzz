<?php 

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class  StatsService{

    public function __construct(private EntityManagerInterface $manager)
    {}

    /**
     * Permet de récup le nombre d'utilisateur enregistré sur mon site
     *
     * @return integer|null
     */
    public function getUsersCount(): ?int
    {
        return $this->manager->createQuery("SELECT COUNT(u) FROM App\Entity\User u")->getSingleScalarResult();
    }

    /**
     * Permet de récup le nombre d'équipes
     *
     * @return integer|null
     */
    public function getTeamsCount(): ?int
    {
        return $this->manager->createQuery("SELECT COUNT(a) FROM App\Entity\Team a")->getSingleScalarResult();
    }

}

