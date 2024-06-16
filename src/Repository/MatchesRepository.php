<?php

namespace App\Repository;

use App\Entity\Matches;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Matches>
 */
class MatchesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Matches::class);
    }
    
    public function searchMatches(string $query): array
    {
        return $this->createQueryBuilder('m')
            ->join('m.homeTeam', 'home')
            ->join('m.awayTeam', 'away')
            ->andWhere('home.name LIKE :query OR away.name LIKE :query OR CONCAT(home.name, \' - \', away.name) LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }
    

    public function findSeasonMatches($homeTeam, $awayTeam, $startDate, $endDate)
    {
        return $this->createQueryBuilder('m')
            ->where('m.homeTeam = :homeTeam AND m.awayTeam = :awayTeam')
            ->andWhere('m.date >= :startDate')
            ->andWhere('m.date <= :endDate')
            ->setParameter('homeTeam', $homeTeam)
            ->setParameter('awayTeam', $awayTeam)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Matches[] Returns an array of Matches objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Matches
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
