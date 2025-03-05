<?php

namespace App\Repository;

use App\Entity\Articlelike;
use App\Entity\Article;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Articlelike>
 */
class ArticlelikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Articlelike::class);
    }

       

    public function findbyalllike(): array
    {
        // $qb=$this->createQueryBuilder('article')
        //          ->getQuery()
        //          ->getResult();
         
        //  return $qb;
        $conn=$this->getEntityManager()->getConnection();
        $sql='SELECT * FROM articlelike';
        $stmt = $conn->executeQuery($sql);
        $result = $stmt->fetchAllAssociative();
        return $result;
    }
    //    public function findOneBySomeField($value): ?Articlelike
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
