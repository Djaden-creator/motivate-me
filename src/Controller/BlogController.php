<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Abonner;
use App\Entity\Article;
use App\Entity\Religion;
use App\Entity\Motivateur;
use App\Entity\Articlelike;
use App\Repository\UserRepository;
use App\Repository\AbonnerRepository;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ArticlelikeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\ArgumentResolver\EntityValueResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{

    private $entityManager;
    public function __construct( EntityManagerInterface $entityManagerInterface)
    {
       $this->entityManager=$entityManagerInterface;
    }

    // here we are getting all product on the page(articles)
    #[Route('/blog', name: 'app_blog')]
    public function index(EntityManagerInterface $entityManager):response
    {
      $session=$this->getUser();
      //here all demande not yet threated for the admin
      $demandes=$entityManager->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);

      //we are getting the user demande to know if the decision is accepted he has to see add article button and etc...
      $validedemandes=$entityManager->getRepository(Motivateur::class)->findby(['user'=>$session,'decision'=>'acceptée']);

      $religion=$entityManager->getRepository(Religion::class)->findAll();
      if(isset($session))
      {
        $user = $entityManager->getRepository(User::class)->find($session);
        $testuser=$user->getReligion();
        $articleall=$entityManager->getRepository(Article::class);
        $articles=$articleall->findby(['category'=>$testuser]);
      }else {
        $articleall=$entityManager->getRepository(Article::class);
        $articles=$articleall->findAll();
        shuffle($articles);
      } 
       return $this->render('blog/index.html.twig', [
        'articles'=> $articles, 
        'religions'=>$religion,
        'users'=>$user,
        'demandes'=>$demandes,
        'validedemandes'=>$validedemandes
    ]);
    }
     
    // here the user can delete his article only if he is the one who posted it a single article on the main page
    #[Route('/delete/{id}', name: 'app_delete')]
    public function delete($id,EntityManagerInterface $entityManager,ArticleRepository $repos):response
    {
        $articles=$repos->find($id);
        $entityManager ->remove($articles);
        $entityManager ->flush();
    
        return $this->redirectToroute('app_mypublication');
    }

    /**
     * Summary of GetLike of an existing article
     * likes and dislike
     * @param \App\Entity\Article $article
     * @param \App\Repository\ArticlelikeRepository $repolike
     * @param \Doctrine\Persistence\ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/article/{id}/like', name: 'likeitem')]
    public function Like(UserInterface $userInterface,Article $publication,ArticlelikeRepository $repolike,EntityManagerInterface $manager):Response
    {
        
        if(!$userInterface){
            return $this->json(['code'=>415,'message'=>'not connected'],403);
        }
        
        if($publication->IslikeByUser($userInterface))
        {
           $articlelike=$repolike->findOneBy([
               'postid'=>$publication,
               'userid'=>$userInterface
           ]);
           $manager->remove($articlelike);
           $manager->flush();

           return $this->json([
            'code'=>200,
            'message'=>'like bien supprimé',
            'articlelike'=>$repolike->count([
                'postid'=>$publication
            ],200)
        ]);
        }
        
        $articlelike=new Articlelike();
        $articlelike->setPostid($publication)
                    ->setUserid($userInterface);
             $manager->persist($articlelike);
             $manager->flush();
        return $this->json(
            ['code'=>200,
                   'message'=>'like ajouté',
                   'articlelike'=>$repolike->count([
                   'postid'=>$publication
            ],200)
                  ],200);
    }
}
