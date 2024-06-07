<?php

namespace App\Repository;

use App\Entity\News;
use App\Entity\Team;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<News>
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    //    /**
    //     * @return News[] Returns an array of News objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('n.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?News
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findPreviousNews(int $id):?News
    {
        return $this->createQueryBuilder('n')
            ->where('n.id < :id')
            ->setParameter('id', $id)
            ->orderBy('n.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    public function findNextNews(int $id):?News
    {
        return $this->createQueryBuilder('n')
            ->where('n.id > :id')
            ->setParameter('id', $id)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByNewsTitle(string $term): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->where('a.title LIKE :term ')  // Rechercher par titre ou nom de l'auteur
            ->setParameter('term', '%' . $term . '%');
    }

}
