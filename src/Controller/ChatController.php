<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Message;
use App\Entity\Following;
use App\Form\MessageType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ChatController extends AbstractController
{
    #[Route('/mixtchat', name: 'app_mixtchat')]
    public function allchat(EntityManagerInterface $entityManagerInterface): Response
    {
        // we getting all message new send for  this user in the aside bar
        $getmessages=$entityManagerInterface->getRepository(Message::class)->findby(['usertwo'=>$this->getUser()]);
        return $this->render('chat/index.html.twig', [
            'getmessages' => $getmessages,
        ]);
    }

    #[Route('/chat/{id}', name: 'app_chat')]
    public function index($id,request $request,EntityManagerInterface $entityManagerInterface,ManagerRegistry $managerRegistry): Response
    { 
        $followingcounts=$entityManagerInterface->getRepository(Following::class)->findby(['usersessionid'=>$this->getUser()]);
        
        $usertwo=$entityManagerInterface->getRepository(User::class)->find($id);

        //we are geting all my messages and a particular motivator
        $getmychat=$entityManagerInterface->getRepository(Message::class)->findBy([
            'userone'=>$this->getUser(),
            'usertwo'=>$usertwo,
        ]);
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         $test=$form->get('content')->getData();
         
            $message->setDate(new \DateTime())
                      ->setUserone($this->getUser())
                      ->setUsertwo($usertwo);
                      if($em=$managerRegistry->getManager()){
                        $em->persist($message);
                        $em->flush();
                        return new JsonResponse(['status' => 'success', 'message' => 'motivator code bien enregistré']);
                      }else{
                        return new JsonResponse(['status' => 'error', 'errors' => 'very bad request']);
                      }
            
        }
        return $this->render('chat/indextwo.html.twig', [
            'usernames' => $usertwo,
            'followingcounts' => $followingcounts,
            'form' => $form->createView(),
            'getmychat'=>$getmychat,
        ]);
    }
    // here to search a motivator that am following only
    #[Route('/getmotivator', name: 'getmotivator', methods: ['GET'])]
    public function search(Request $request,EntityManagerInterface $entityManagerInterface) 
    {
      // here we are searching the user motivator with decision = accepted to be motivator 
      $query = $request->query->get('query', '');
      $qb=$entityManagerInterface->createQueryBuilder();
      $user=$this->getUser();
      $qb->select('u')
      ->from(User::class,'u')
      ->innerJoin(Following::class,'f', 'WITH','f.offuserid=u.id')
      ->where('f.usersessionid = :user')
      ->andWhere('u.username LIKE :query OR u.email LIKE :query')
      ->setParameter('query','%'.$query.'%')
      ->setParameter('user',$user)
      
      ->setMaxResults(3);
      $results=$qb->getQuery()->getResult();
        
        return $this->render('chat/getresult.html.twig',[
          'results'=>$results
        ]);
    }
}
