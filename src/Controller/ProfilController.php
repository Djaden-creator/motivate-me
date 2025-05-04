<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Contact;
use App\Entity\Message;
use App\Entity\Following;
use App\Entity\Addingroup;
use App\Entity\Motivateur;
use App\Entity\Notification;
use App\Entity\Shareingroup;
use App\Repository\MotivateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(EntityManagerInterface $em): Response
    {
        $users=$em->getRepository(User::class)->find($this->getUser());

        // this code is to get all message unread for the user
        $getunread=$em->getRepository(Message::class)->findBy([
            'usertwo'=>$this->getUser(),
            'status'=>"unread",
          ]);

        // this code is about to fetch all demande of user not yet threated by the admin new demande
        $demandes=$em->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);

        //here we are getting a user demande for a particular user
        $userdemandes=$em->getRepository(Motivateur::class)->findby(['user'=>$this->getUser()]);

        // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
        $validedemandes=$em->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);
       
         // we are getting all motivator count that this user online following
         $followingcount=$em->getRepository(Following::class)->findby(['usersessionid'=>$this->getUser()]);
        
       //this code allow us to get all inquiry not responded with the status of null
       $getallinquery=$em->getRepository(Contact::class)->findBy(['status'=>null]);

       return $this->render('profil/index.html.twig', [
            'users' => $users,
            'demandes'=> $demandes,
            'validedemandes'=>$validedemandes,
            'userdemandes'=>$userdemandes,
            'followingcount'=> $followingcount,
            'unreadmessage'=> $getunread,
            'getallinquery'=>$getallinquery // on ajoute cette ligne pour afficher les demandes non répondues par l'utilisateur courant  //
        ]);
    }

    #[Route('/delete_demande/{id}', name: 'app_delete_demande')]
    public function deleteDemand($id,EntityManagerInterface $entityManager,MotivateurRepository $motivateurRepository):response
    {
        $demande=$motivateurRepository->find($id);
        $entityManager ->remove($demande);
        $entityManager ->flush();
    
        return $this->redirectToroute('app_profil');
    }

    // here we get all user group
    #[Route('/usergroup', name: 'app_usergroup')]
    public function usergroup(EntityManagerInterface $entityManager):response
    {

        $users=$entityManager->getRepository(User::class)->find($this->getUser());

        // this code is to get all message unread for the user
        $getunread=$entityManager->getRepository(Message::class)->findBy([
            'usertwo'=>$this->getUser(),
            'status'=>"unread",
          ]);

        // this code is about to fetch all demande of user not yet threated by the admin new demande
        $demandes=$entityManager->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);

        //here we are getting a user demande for a particular user
        $userdemandes=$entityManager->getRepository(Motivateur::class)->findby(['user'=>$this->getUser()]);

        // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
        $validedemandes=$entityManager->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);
       
         // we are getting all motivator count that this user online following
         $followingcount=$entityManager->getRepository(Following::class)->findby(['usersessionid'=>$this->getUser()]);
         // here we get this user  in the adding group means in his group 
         $usergroup=$entityManager->getRepository(Addingroup::class)->findBy([
            'newmember'=>$this->getUser()
        ]);
        if (!empty($usergroup)) {
            // If $usergroup is not empty, access the first element means we get is group id 
            $goupenttity = $usergroup[0]->getGroupid();
        
            // Count how many articles are in this group
            $countarticle = $entityManager->getRepository(Shareingroup::class)->findBy([
                'groupid' => $goupenttity
            ]);
        
            // we get the new post with the notification unread by this user for his group 
           //we are geting  amm new notification of new post in the group where the user did not yet read it
            $unreadnotification=$entityManager->getRepository(Notification::class)->findBy([
                'usertonotifie'=>$users,
                'IsRead'=>"0",
            ]);
        } else {
            // Handle the case where $usergroup is empty
            $goupenttity = null;
            $countarticle = [];
            $unreadnotification = [];
        }
        
         //this code allow us to get all inquiry not responded with the status of null
       $getallinquery=$entityManager->getRepository(Contact::class)->findBy(['status'=>null]);
      
        return $this->render('profil/mygroup.html.twig',[
            'users' => $users,
            'demandes'=> $demandes,
            'validedemandes'=>$validedemandes,
            'userdemandes'=>$userdemandes,
            'followingcount'=> $followingcount,
            'unreadmessage'=> $getunread,
            'mygroups'=>$usergroup,
            'countarticle'=>$countarticle,
            'status'=>$unreadnotification,
            'getallinquery'=>$getallinquery 
        ]);
    }
}
