<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Sauvegarde;
use App\Repository\SauvegardeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SauvegardController extends AbstractController
{
    #[Route('/sauvegard/{id}', name: 'app_sauvegard')]
    public function likereply(UserInterface $userInterface,Article $article,SauvegardeRepository $sauvegardeRepository,EntityManagerInterface $entityManagerInterface):Response
    {
        if(!$userInterface){
            return $this->json(['code'=>415,'message'=>'not connected'],403);
        }

        //if the user already saved the article,we delete it in the database
        if($article->IsSavedByUser($userInterface))
        {
            $savedarticle=$sauvegardeRepository->findOneBy([
                'article'=>$article,
                'user'=>$userInterface
            ]);
            $entityManagerInterface->remove($savedarticle);
            $entityManagerInterface->flush();
 
            return $this->json([
             'code'=>200,
             'message'=>'sauvegarde bien supprimé',
             'savedarticle'=>$sauvegardeRepository->count([
                 'article'=>$article
             ],200)
         ]);
        }
        //if the user hasn't saved the article,we add it in the database
        $savedarticle=new Sauvegarde();
        $savedarticle->setArticle($article)
                     ->setUser($userInterface)
                     ->setDate(new \DateTime());
             $entityManagerInterface->persist($savedarticle);
             $entityManagerInterface->flush();
        return $this->json(
            ['code'=>200,
                   'message'=>'article bien sauvegardé',
                   'savedarticle'=>$sauvegardeRepository->count([
                   'article'=>$article
            ],200)
                  ],200);
    }
}
