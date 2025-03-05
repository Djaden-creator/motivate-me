<?php

namespace App\Repository;

use App\Entity\Commentaires;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commentaires>
 */
class CommentairesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commentaires::class);
    }

       /**
        * @return Commentaires[] Returns an array of Commentaires objects
        */
       public function findByExampleField($value=null): array
       {
           return $this->createQueryBuilder('c')
               ->orderBy('c.id', 'DESC')
               ->andWhere('c.idarticle = :value')
               ->setParameter('value', $value)
               ->getQuery()
               ->getResult()
           ;
       }



       public function findOneBySomeField($value): ?Commentaires
       {
           return $this->createQueryBuilder('c')
               ->andWhere('c.id = :value')
               ->setParameter('value', $value)
               ->getQuery()
               ->getOneOrNullResult()
           ;
       }
}
