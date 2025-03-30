<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Contact;
use App\Entity\Message;
use App\Entity\Following;
use App\Entity\Motivateur;
use App\Repository\UserRepository;
use App\Repository\FollowingRepository;
use App\Repository\MotivateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class FollowerController extends AbstractController
{

    // here we are following and unfollow the motivator
    #[Route('/follow/{id}', name: 'follow_user')]
    public function follow($id,User $followed,EntityManagerInterface $em,FollowingRepository $followingRepository): Response
    {
        // if the user is not logged in, he can't follow anyone
        $session=$this->getUser();
        $useronline=$em->getRepository(User::class)->find($session);
        
        if (!$session) {
            return $this->json(['message' => 'Not logged in'], 403);
        }
        if($useronline->isFollowing($followed)){
            $newfollower=$followingRepository->findOneBy([
                'usersessionid'=>$session,
                 'offuserid'=>$followed,
            ]);
            $em->remove($newfollower);
            $em->flush();

            return $this->json(
                [
                       'message'=>'Suivre',
                ],200);
        }
        
          
        $newfollower=new Following();
        $newfollower->setUsersessionid($session)
                    ->setOffuserid($followed)
                    ->setDate(new \DateTime());
             $em->persist($newfollower);
             $em->flush();
        return $this->json(
            [
                   'message'=>'Abonné',
            ],200);
    }

    // we are getting the list of all motivator that am following
    #[Route('/mymotivator_list', name: 'app_mymotivatorlist')]
    public function followingCount(EntityManagerInterface $entityManagerInterface)
    {

        $users=$entityManagerInterface->getRepository(User::class)->find($this->getUser());

        // this code is about to fetch all demande of user not yet threated by the admin new demande
        $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);

        //here we are getting a user demande for a particular user
        $userdemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$this->getUser()]);

        //this code allow us to get all inquiry not responded with the status of null
        $getallinquery=$entityManagerInterface->getRepository(Contact::class)->findBy(['status'=>null]);

        // here we are geting if the user got a new unread message in the nav bar
        $getunread=$entityManagerInterface->getRepository(Message::class)->findBy([
        'usertwo'=>$this->getUser(),
        'status'=>"unread",
        ]);
        // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
        $validedemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);
      // we are getting all motivator count that this user online following
        $followingcounts=$entityManagerInterface->getRepository(Following::class)->findby(['usersessionid'=>$this->getUser()]);
      return $this->render('follower/index.html.twig',[
        'followingcounts'=>$followingcounts,
        'users' => $users,
        'demandes'=> $demandes,
        'validedemandes'=>$validedemandes,
        'userdemandes'=>$userdemandes,
        'unreadmessage'=> $getunread,
        'getallinquery'=>$getallinquery,
        
      ]);
    }

    // here the user online trying to see the profil of motivator tha is following
    #[Route('/motivator_profil/{id}', name: 'app_motivator_profil')]
    public function motivatorprofil($id,EntityManagerInterface $entityManagerInterface)
    {

        $users=$entityManagerInterface->getRepository(User::class)->find($this->getUser());

        // this code is about to fetch all demande of user not yet threated by the admin new demande
        $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);

        //here we are getting a user demande for a particular user
        $userdemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$this->getUser()]);

        //this code allow us to get all inquiry not responded with the status of null
        $getallinquery=$entityManagerInterface->getRepository(Contact::class)->findBy(['status'=>null]);

        // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
        $validedemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);
      
        // here we are getting the user 
        $getusers=$entityManagerInterface->getRepository(User::class)->findby(['id'=>$id]);
        // here we are getting the article 
        $articles=$entityManagerInterface->getRepository(Article::class)->findby(['userposter'=>$id]);
        $getunread=$entityManagerInterface->getRepository(Message::class)->findBy([
          'usertwo'=>$this->getUser(),
          'status'=>"unread",
        ]);        
      return $this->render('follower/motivator_profil.html.twig',[
        'users' => $users,
        'demandes'=> $demandes,
        'validedemandes'=>$validedemandes,
        'userdemandes'=>$userdemandes,
        'getusers'=>$getusers,
        'articles'=>$articles,
        'unreadmessage'=> $getunread,
        'getallinquery'=>$getallinquery,

      ]);
    }

    // here to search a motivator

    #[Route('/search', name: 'search', methods: ['GET'])]
    public function search(Request $request, UserRepository $userRepository,EntityManagerInterface $entityManagerInterface) 
    {
      // here we are searching the user motivator with decision = accepted to be motivator 
        $query = $request->query->get('query', '');

        $qb=$entityManagerInterface->createQueryBuilder();
        $qb->select('u')
        ->from(User::class,'u')
        ->innerJoin(Motivateur::class,'m', 'WITH','m.user=u.id')
        ->where('m.decision = :decision')
        ->andWhere('u.username LIKE :query OR u.email LIKE :query')
        ->setParameter('query','%'.$query.'%')
        ->setParameter('decision',"acceptée")
        
        ->setMaxResults(3);
        $results=$qb->getQuery()->getResult();
        
        return $this->render('follower/ajaxresult.html.twig',[
          'results'=>$results
        ]);
    }
    
}
