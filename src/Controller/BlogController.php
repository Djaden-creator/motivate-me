<?php

namespace App\Controller;

use Exception;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\Contact;
use App\Entity\Message;
use App\Entity\Religion;
use App\Entity\Motivateur;
use App\Entity\Articlelike;
use App\Entity\Notification;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ArticlelikeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
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

      // here we are geting if the user got a new unread message in the nav bar
      $getunread=$entityManager->getRepository(Message::class)->findBy([
        'usertwo'=>$this->getUser(),
        'status'=>"unread",
    ]);

    //we are getting the notification when the new post was added in the group
    $notification=$entityManager->getRepository(Notification::class)->findBy([
        'usertonotifie'=>$session,
        'IsRead'=>false
    ]);

    // here we are geting if the user got a article in the group if he did not yet read it count it
    $getunread=$entityManager->getRepository(Message::class)->findBy([
        'usertwo'=>$this->getUser(),
        'status'=>"unread",
    ]);
    
      //this code allow us to get all inquiry not responded with the status of null
      $getallinquery=$entityManager->getRepository(Contact::class)->findBy(['status'=>null]);
      
      $religion=$entityManager->getRepository(Religion::class)->findAll();
      if(isset($session))
      {
        $user = $entityManager->getRepository(User::class)->find($session);
        $testuser=$user->getReligion();
        $articles=$entityManager->getRepository(Article::class)->findby(['category'=>$testuser],['id'=>'DESC'],15);
        shuffle($articles);
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
        'validedemandes'=>$validedemandes,
        'unreadmessage'=> $getunread,
        'getallinquery'=>$getallinquery,
        'notification'=>$notification,
    ]);
    }
     
    // here the user can delete his article only if he is the one who posted it a single article on the main page
    #[Route('/delete/{id}', name: 'app_delete')]
    public function delete($id,EntityManagerInterface $entityManager,ArticleRepository $repos):response
    {
        try {
            $articles=$repos->find($id);
            $entityManager ->remove($articles);
            $entityManager ->flush();
    
        return $this->redirectToroute('app_mypostes');
        } catch (Exception $e) {
            return new response("sorry you cant delete this article beacause it has some orders on it");
        }
    
        return $this->redirectToroute('app_mypostes');
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
    public function Like(UserInterface $userInterface,Article $publication,ArticlelikeRepository $repolike,EntityManagerInterface $manager): \Symfony\Component\HttpFoundation\Response
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
