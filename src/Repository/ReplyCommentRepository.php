<?php

namespace App\Repository;

use App\Entity\ReplyComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReplyComment>
 */
class ReplyCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReplyComment::class);
    }

       /**
        * @return ReplyComment[] Returns an array of ReplyComment objects
        */
       public function findByExampleField($value): array
       {
           return $this->createQueryBuilder('r')
               ->andWhere('r.comment = :value')
               ->setParameter('value', $value)
               ->orderBy('r.id', 'DESC')
               ->getQuery()
               ->getResult()
           ;
       }

    //    public function findOneBySomeField($value): ?ReplyComment
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
