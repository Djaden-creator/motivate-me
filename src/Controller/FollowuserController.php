<?php

namespace App\Controller;

use App\Entity\Follower;
use App\Entity\User;
use App\Repository\FollowerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class FollowuserController extends AbstractController
{
    #[Route('/followuser', name: 'app_followuser')]
    public function index(UserRepository $userRepository): Response
    {
        $users=$userRepository->findByExampleField();
        shuffle($users);
        return $this->render('followuser/index.html.twig', [
            'controller_name' => 'FollowuserController',
            'users'=> $users
        ]);
    }

    #[Route('/followuser/{id}', name: 'app_userfollow')]
    public function followeruser(UserInterface $userInterface,User $user, $id,EntityManagerInterface $manager,FollowerRepository $followerRepository): Response
    {
       
        if(!$userInterface){
            return $this->json(['code'=>415,'message'=>'vous etes hors net'],403);
        }
        elseif($user->isFollowedBy($userInterface))
        {
           $newfollower=$followerRepository->findOneBy([
              'sessionuser'=>$userInterface,
              'offsessionuser'=>$user,
           ]);
           $manager->remove($newfollower);
           $manager->flush();

           return $this->json([
            'code'=>200,
            'message'=>'Follow',
            'newfollower'=>$followerRepository->count([
            'sessionuser'=>$userInterface,
            'offsessionuser'=>$user
            ],200)
        ]);
        } 
        $newfollower= new Follower();
        $newfollower->setSessionuser($userInterface)
                    ->setOffsessionuser($user)
                    ->setFriendship('processing');
                     $manager->persist($newfollower);
                     $manager->flush();
                return $this->json(
                    ['code'=>200,
                           'message'=>'Unfollow',
                           'newfollower'=>$followerRepository->count([
                           'sessionuser'=>$userInterface,
                           'offsessionuser'=>$user
                    ])
                          ],200);
            
       
    }
}
