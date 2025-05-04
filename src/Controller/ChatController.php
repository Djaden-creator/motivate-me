<?php

namespace App\Controller;

use PDO;
use Exception;
use App\Entity\User;
use App\Entity\Contact;
use App\Entity\Message;
use App\Entity\Following;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ChatController extends AbstractController
{
    #[Route('/mixtchat', name: 'app_mixtchat')]
    public function allchat(EntityManagerInterface $entityManagerInterface): Response
    {

        // we getting all message new and old send for  this user in the aside bar
        //to get the user in the message table we need to call identity methode to extract the user id in the message table
        $me=$this->getUser();

        //this code allow us to get all inquiry not responded with the status of null
        $getallinquery=$entityManagerInterface->getRepository(Contact::class)->findBy(['status'=>null]);
        
        $qb=$entityManagerInterface->getRepository(Message::class)
        ->createQueryBuilder('m')
        ->select('m')
        ->orderBy('m.date','DESC')
        ->groupBy('m.userone')

        // ->select('DISTINCT IDENTITY(m.userone),m.status,IDENTITY(m.usertwo),m.content')
        ->where("m.usertwo=:me")
        ->setParameter('me',$me)
        ->getQuery()
        ->getResult();
      
        return $this->render('chat/index.html.twig', [
            // 'getmessages' => $getmessages,
            'getmessage'=>$qb,
            'getallinquery'=>$getallinquery,
           
        ]);
    //    
    }

    // this code allows us to get the conversation when we clicked on the user 
    #[Route('/chat/{id}', name: 'app_chat')]
    public function index($id,request $request,EntityManagerInterface $entityManagerInterface,ManagerRegistry $managerRegistry): Response
    {
        
        $followingcounts=$entityManagerInterface->getRepository(Following::class)->findby(['usersessionid'=>$this->getUser()]);
        
        $sender=$entityManagerInterface->getRepository(User::class)->find($id);
        $getid=$sender->getId();
        $user=$this->getUser();

        //this code allow us to get all inquiry not responded with the status of null
        $getallinquery=$entityManagerInterface->getRepository(Contact::class)->findBy(['status'=>null]);

        //here am getting all message sent by this $sender to me and send to me for him
        $qb=$entityManagerInterface->getRepository(Message::class)
        ->createQueryBuilder('m')
        ->select('m')
        ->where("m.userone=:sender AND m.usertwo=:user OR m.userone=:user AND m.usertwo=:sender ")
        ->setParameter('sender',$sender)
        ->setParameter('user',$user)
        ->orderBy('m.id','ASC')
        ->getQuery()
        ->getResult();

         // we getting all message new send for  this user in the aside bar with the status unread
         $getmessages=$entityManagerInterface->getRepository(Message::class)->findby(['userone'=>$sender,'usertwo'=>$this->getUser(),'status'=>'unread']);
        // here we are updating all messages unread to read status
        if($getmessages)
        {
            foreach ($getmessages as $message) {
                $message->setStatus('read');
                $entityManagerInterface->persist($message);
                $entityManagerInterface->flush();
            }
        }

         //start here we are updating the nombre de vue of an article
        return $this->render('chat/getuserchat.html.twig', [
            'usernames' => $sender,
            'followingcounts' => $followingcounts,
            'id'=>$getid,
            'mychats'=>$qb,
            'getallinquery'=>$getallinquery,
        ]);
    }

    // here we are sending the message live with ajax
     #[Route('/newchat', name: 'app_newchat')]
     public function newchat(EntityManagerInterface $entityManagerInterface): Response
     {
         $dsn = 'mysql:host=localhost;dbname=blogtest';
         $username = 'root';
         $password = getenv('');
         
         //this code allow us to get all inquiry not responded with the status of null
         $getallinquery=$entityManagerInterface->getRepository(Contact::class)->findBy(['status'=>null]);

         $pdo = new PDO($dsn,$username,$password);
         $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   
         if(isset($_POST['messageme'])){
 
           $id=$_POST['id'];
           $messages=$_POST['message'];
           $userid=$_POST['mevalue'];
           $stat='unread';

           // we are sending the message live with ajax by using pdo and prepare statement.
           $sql="INSERT INTO `message`(`userone_id`,`usertwo_id`,`content`,`date`,`status`) VALUES (:userid,:id,:messages,NOW(),:stat)";
           $statement=$pdo->prepare($sql);
           $statement->bindParam(':userid',$userid);
            $statement->bindParam(':id',$id);
            $statement->bindParam(':messages',$messages);
            $statement->bindParam(':stat',$stat);

            if($statement->execute()){
                $getmessages=$entityManagerInterface->getRepository(Message::class)->findBy(['userone'=>$userid,'usertwo'=>$id],['id'=>'DESC'],1);
                return $this->render('chat/welcomroom.html.twig',[
                    'messages'=>$getmessages,
                    'getallinquery'=>$getallinquery,
                ]);
           }
           else{
             return new Response('failed sending');
           }
           };
     
         return new Response("the network is down comme after");
     }
    // here to search a motivator that am following only
    #[Route('/getmotivator', name: 'getmotivator', methods: ['GET'])]
    public function search(Request $request,EntityManagerInterface $entityManagerInterface) 
    {
      // here we are searching the user motivator with decision = accepted to be motivator 
      $query = $request->query->get('query', '');

      //this code allow us to get all inquiry not responded with the status of null
      $getallinquery=$entityManagerInterface->getRepository(Contact::class)->findBy(['status'=>null]);

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
          'results'=>$results,
          'getallinquery'=>$getallinquery,
        ]);
    }


    #[Route('/deleteallmessage/{id}', name: 'app_deleteallmessage')]
    public function deleteallmessage($id,EntityManagerInterface $entityManagerInterface) 
    {
      // here we are searching the user motivator with decision = accepted to be motivator 
      try {
        $sender=$entityManagerInterface->getRepository(User::class)->find($id);
        $user=$this->getUser();

        //this code allow us to get all inquiry not responded with the status of null
        $getallinquery=$entityManagerInterface->getRepository(Contact::class)->findBy(['status'=>null]);

        $qb=$entityManagerInterface->getRepository(Message::class)
        ->createQueryBuilder('m')
        ->select('m')
        ->where("m.userone=:sender AND m.usertwo=:user OR m.userone=:user AND m.usertwo=:sender")
        ->setParameter('sender',$sender)
        ->setParameter('user',$user)
        ->getQuery()
        ->getResult();
        
        foreach ($qb as $message) {
            $entityManagerInterface->remove($message);
            $entityManagerInterface->flush();
        }
        return $this->render('chat/notfund.html.twig',[
            'sender'=>$sender,
            'getallinquery'=>$getallinquery,
        ]);
      } catch (Exception $e) {
        return new response("sorry you cant delete this article beacause it has some orders on it");
      }

    return $this->redirectToroute('');
    }
}
