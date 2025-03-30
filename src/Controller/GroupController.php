<?php

namespace App\Controller;

use PDO;
use App\Entity\User;
use App\Entity\Groups;
use App\Entity\Contact;
use App\Entity\Message;
use App\Entity\Addingroup;
use App\Entity\Motivateur;
use App\Entity\Shareingroup;
use App\Form\CreategroupType;
use App\Form\ShareingroupcontentType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

final class GroupController extends AbstractController
{

    // the landing of group
    #[Route('/group', name: 'app_group')]
    public function index(EntityManagerInterface $entityManager): Response
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
    
      //this code allow us to get all inquiry not responded with the status of null
      $getallinquery=$entityManager->getRepository(Contact::class)->findBy(['status'=>null]);
        return $this->render('group/index.html.twig', [
            'controller_name' => 'GroupController',
            'users'=>$session,
            'demandes'=>$demandes,
            'validedemandes'=>$validedemandes,
            'unreadmessage'=> $getunread,
            'getallinquery'=>$getallinquery
        ]);
    }

     // to get all group for a particular motivator
     #[Route('/allmygroup', name: 'app_allmygroups')]
     public function allmygroup(EntityManagerInterface $entityManager): Response
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

       //here i get my groups as the motivator
       $mygroups=$entityManager->getRepository(Groups::class)->findBy(['userid'=>$session]);
       //this code allow us to get all inquiry not responded with the status of null
       $getallinquery=$entityManager->getRepository(Contact::class)->findBy(['status'=>null]);
         return $this->render('group/allmygroup.html.twig', [
             'controller_name' => 'GroupController',
             'users'=>$session,
             'demandes'=>$demandes,
             'validedemandes'=>$validedemandes,
             'unreadmessage'=> $getunread,
             'getallinquery'=>$getallinquery,
             'groups'=>$mygroups
         ]);
     }
 

     // to get all group for a particular motivator
     #[Route('/getagroup/{id}', name: 'app_getagroup')]
     public function getagroup(Request $request,SluggerInterface $slugger, $id,EntityManagerInterface $entityManager,ManagerRegistry $save): Response
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

      //here i get my groups as the motivator
      $mygroups=$entityManager->getRepository(Groups::class)->find($id);

      //getting the name of the group to create a folder where the poste image will be saved 
       $nameofgroup=$mygroups->getGroupname();
      // here we are creating the form and post functionality to post in the group
       $contentgroup = new Shareingroup();
       $form = $this->createForm(ShareingroupcontentType::class,$contentgroup);
       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {
        $file = $form->get('image')->getData();
        // this condition is needed because the 'brochure' field is not required
        // so the PDF file must be processed only when a file is uploaded
        if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
            $directory=$this->getParameter('groupimage');
            $destinationfile=''.$directory.'/'.$nameofgroup.'';
            $target_file=$directory.basename($newFilename);

            // Move the file to the directory where brochures are stored
            try {
               
                if (file_exists($directory)) {
                    $file->move($directory,$newFilename);

                    if (!file_exists ($destinationfile)) {
                        mkdir($destinationfile,0777,true);
                    } 
                    if (file_exists($destinationfile)) {
                        $source='imagegroupcontent/'.$newFilename;
                        $to='imagegroupcontent/'.$nameofgroup.'/'.$newFilename;
                        copy($source,$to);
                    }
                }
                if (!file_exists ($destinationfile)) {
                    mkdir($destinationfile,0777,true);
                    }
                    $file->move($destinationfile, $newFilename);
                
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
           $contentgroup->setImage($newFilename)
                        ->setPosterownerid($session)
                        ->setGroupid($mygroups)
                        ->setDate(new \DateTime());
        }
           $em=$save->getManager();
           $em->persist($contentgroup);
           $em->flush();
           return $this->redirectToRoute('app_getagroup', ['id' => $mygroups->getId()]);
       }

        //here we get all user in the plateforme include motivator
        $alluser=$entityManager->getRepository(User::class)->findAll();
        //this code allow us to get all inquiry not responded with the status of null
        $getallinquery=$entityManager->getRepository(Contact::class)->findBy(['status'=>null]);

        //here we get all user in the plateforme include motivator
        $alluser=$entityManager->getRepository(User::class)->findAll();

        // am getting all post of the group
        $allgrouppost=$entityManager->getRepository(Shareingroup::class)->findBy([
          'groupid'=>$mygroups,
        ]);
    
        //this code allow us to get all inquiry not responded with the status of null
        $getallinquery=$entityManager->getRepository(Contact::class)->findBy(['status'=>null]);
         return $this->render('group/getgroup.html.twig', [
             'controller_name' => 'GroupController',
             'users'=>$session,
             'demandes'=>$demandes,
             'validedemandes'=>$validedemandes,
             'unreadmessage'=> $getunread,
             'getallinquery'=>$getallinquery,
             'groups'=>$mygroups,
             'alluser'=>$alluser,
             'form'=>$form->createView(),
             'allgrouppost'=>$allgrouppost
         ]);
     }
 

      // we are adding a user in the group of a motivator
      #[Route('/adduser', name: 'app_adduser')]
      public function adduser(Request $request,UserInterface $userInterface,EntityManagerInterface $entityManager,ManagerRegistry $save)
      {
        if(isset($_POST['add'])){
          $id=$_POST['id'];
          $group=$_POST['group'];

          $getgroup=$entityManager->getRepository(Groups::class)->find($group);
          $user=$entityManager->getRepository(User::class)->find($id);
          $send= new Addingroup();
          $send->setAddedby($this->getUser())
               ->setGroupid($getgroup)
               ->setNewmember($user)
               ->setDate( new \DateTime());
               $em=$save->getManager();
               $em->persist($send);
               $em->flush();

          return $this->render('group/button.html.twig',['id'=>$id,'group'=>$getgroup]);
        }
      
      }
  
       // we are removing the user a user in the group of a motivator
       #[Route('/removeuser', name: 'app_removeuser')]
       public function removeuser(Request $request,UserInterface $userInterface,EntityManagerInterface $entityManager)
       {
         if(isset($_POST['add'])){
           $id=$_POST['id'];
           $group=$_POST['group'];
           $session=$this->getUser();
           //user online
           $useronline=$entityManager->getRepository(User::class)->find($session);

           // group entity
           $getgroup=$entityManager->getRepository(Groups::class)->find($group);
           //the user to add in the group
           $useroff=$entityManager->getRepository(User::class)->find($id);

           if($useronline->isAdding($useroff)){
            $getuserin=$entityManager->getRepository(Addingroup::class)->findOneBy(['newmember'=>$useroff,'groupid'=>$getgroup,'addedby'=>$useronline]);
            $entityManager->remove($getuserin);
            $entityManager->flush();
           }
           
           return $this->render('group/buttonremove.html.twig',['id'=>$id,'group'=>$getgroup]);
         }
       
       }
    //here to create a group using a form
    #[Route('/create_group', name: 'app_creategroup')]
    public function creategroup(Request $request,EntityManagerInterface $entityManager,ManagerRegistry $save): Response
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
    
      //this code allow us to get all inquiry not responded with the status of null
      $getallinquery=$entityManager->getRepository(Contact::class)->findBy(['status'=>null]);

        $group = new Groups();
        $form = $this->createForm(CreategroupType::class, $group);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $groupname=$form->get('groupname')->getData();
            $regles=$form->get('regle')->getData();
            $group->setDate(new \DateTimeImmutable())
                  ->setUserid($this->getUser());
           
            if(empty($codehere) && empty($regles)){
               return new JsonResponse([
                   'status' => 'error',
                   'errors' =>'tout le champs doivent etre remplis',
               ], 400);
   
            }
           
           $em=$save->getManager();
           $em->persist($group);
           $em->flush();
           return new JsonResponse(['status' => 'success', 'message' => 'felicitation vous venez de creer votre group']);
       }
        
        return $this->render('group/creategroup.html.twig', [
            'controller_name' => 'GroupController',
            'users'=>$session,
            'demandes'=>$demandes,
            'validedemandes'=>$validedemandes,
            'unreadmessage'=> $getunread,
            'getallinquery'=>$getallinquery,
            'form'=>$form->createView()
        ]);
    }


    // here to search a user by his name to add in the group
   #[Route('/searchuserforgroup', name: 'searchuserforgroup', methods: ['GET'])]
   public function search(Request $request,EntityManagerInterface $entityManagerInterface) 
   {
     // here we are searching the user motivator with decision = accepted to be motivator 
      $query = $request->query->get('query', '');
      $groupo=$request->query->get('groupo','');
      //we get the entity fo group
      $getgroup=$entityManagerInterface->getRepository(Groups::class)->find($groupo);

      $qb=$entityManagerInterface->createQueryBuilder();
      $user=$this->getUser();
      $qb->select('u')
      ->from(User::class,'u')
      ->andWhere('u.username LIKE :query OR u.email LIKE :query')
      ->setParameter('query','%'.$query.'%');
      $results=$qb->getQuery()->getResult();
        
        return $this->render('group/result.html.twig',[
          'alluser'=>$results,
          'groupo'=>$getgroup
        ]);   
   }

}
