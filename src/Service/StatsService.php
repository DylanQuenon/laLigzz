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
    /**
     * Permet de récup le nombre d'équipes
     *
     * @return integer|null
     */
    public function getNewsCount(): ?int
    {
        return $this->manager->createQuery("SELECT COUNT(n) FROM App\Entity\News n")->getSingleScalarResult();
    }
    /**
     * Récupère le nombre total de buts marqués sur la saison
     *
     * @return integer|null
     */
    public function getAllGoals(): ?int
    {
        return $this->manager->createQuery("SELECT SUM(r.goalsFor) from App\Entity\Ranking r")->getSingleScalarResult();
    }
    /**
     * Récupère le nombre de matchs
     *
     * @return integer|null
     */
    public function getAllGames(): ?int
    {
        return $this->manager->createQuery("SELECT COUNT(m) from App\Entity\Matches m")->getSingleScalarResult();
    }
    /**
     * Récupère le nombre de points
     *
     * @return integer|null
     */
    public function getAllPoints(): ?int
    {
        return $this->manager->createQuery("SELECT SUM(r.points) from App\Entity\Ranking r")->getSingleScalarResult();
    }

}

