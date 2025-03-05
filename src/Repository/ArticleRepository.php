<?php

namespace App\Repository;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\Articlelike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findByExampleField(User $user): array
       {
           $user->getReligion();
           return $this->createQueryBuilder('a')
             ->select('a.category')
               ->andWhere('a.category = :user')
               ->setParameter('user', $user)
               ->orderBy('a.id', 'DESC')
               ->getQuery()
               ->getResult()
           ;
       }

    public function findbylike(): array
    {
        // $qb=$this->createQueryBuilder('article')
        //          ->getQuery()
        //          ->getResult();
         
        //  return $qb;
        $conn=$this->getEntityManager()->getConnection();
        $sql='SELECT * FROM article inner join user on article.userposter_id=user.id';
        $stmt = $conn->executeQuery($sql);
        $result = $stmt->fetchAllAssociative();
        
        return $result;
        
    }
    public function findbyid(EntityManagerInterface $entityManager,$id)
    {
        $articlesbyids = $entityManager->getRepository(Article::class)->find($id);

        if (!$articlesbyids) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }
       
    }

}
