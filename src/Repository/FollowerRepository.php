<?php

namespace App\Repository;

use App\Entity\Follower;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Follower>
 */
class FollowerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Follower::class);
    }

    //    /**
    //     * @return Follower[] Returns an array of Follower objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //         //    ->andWhere('f.exampleField = :value')
    //         //    ->setParameter('value', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

       public function findOneBySomeField($idsession=null,$offsession=null): ?Follower
       {
           return $this->createQueryBuilder('f')
               ->andWhere('f.sessionuser = :idsession and f.offsessionuser=:offsession')
               ->setParameter('idsession', $idsession)
               ->setParameter('offsession', $offsession)
               ->getQuery()
               ->getOneOrNullResult()
           ;
       }
}
