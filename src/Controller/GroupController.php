<?php

namespace App\Controller;

use App\Entity\Check;
use App\Entity\Commentgrouppost;
use App\Entity\Likepostgroup;
use App\Entity\Votecomment;
use App\Form\CommentgrouppostType;
use App\Repository\CommentgrouppostRepository;
use App\Repository\LikepostgroupRepository;
use App\Repository\VotecommentRepository;
use App\Entity\User;
use App\Entity\Groups;
use App\Entity\Contact;
use App\Entity\Message;
use App\Entity\Addingroup;
use App\Entity\Motivateur;
use App\Entity\Notification;
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

      //getting the id of thegroup
      $getidgroup=$mygroups->getId();

      // here we get the creator of the group
      $creatorofgroup=$mygroups->getUserid();

      // the creator email
      $emailcreator=$creatorofgroup->getEmail();

      //we get the member of a particular group to send them the notification after posting in the group
      $getmembers=$entityManager->getRepository(Addingroup::class)->findby([
        'groupid'=>$id,
      ]);
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
                        ->setStatus('unseen')
                        ->setDate(new \DateTime());
        }
           $em=$save->getManager();
           $em->persist($contentgroup);
           //notify all member in the group
           if ($getmembers) {
            foreach ($getmembers as $member) {
              $notification= new Notification();
              $notification->setUsertonotifie($member->getNewmember())
                           ->setMessage("New poste was added in your group '{$nameofgroup}' ")
                           ->setIsRead(false)
                           ->setDate(new \DateTime())
                          
                           ->setShareingroupid($contentgroup);
              $em->persist($notification);
            }
           }
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
        ], ['id'=>'DESC'], 5);
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
             'allgrouppost'=>$allgrouppost,
             'creator'=>$creatorofgroup,
             'themail'=>$emailcreator,
             'idgroup'=>$getidgroup,
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

      //we create a profil view for member of a group
      #[Route('/member_profil/{id}', name: 'app_member_profil')]
      public function memberprofil($id,EntityManagerInterface $entityManager)
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

        $getmember=$entityManager->getRepository(User::class)->find($id);

        //get group of the user
        $show=$entityManager->getRepository(Addingroup::class)->findBy(['newmember'=>$getmember]);

          return $this->render('group/profil_member.html.twig', [
            'controller_name' => 'GroupController',
            'users'=>$session,
            'demandes'=>$demandes,
            'validedemandes'=>$validedemandes,
            'unreadmessage'=> $getunread,
            'getallinquery'=>$getallinquery,
            'members'=>$getmember,
            'show'=>$show
        ]);
      
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

   // here we delete the post group
   #[Route('/deletepostgroup/{id}', name: 'app_deletepostgroup')]
   public function deletepostgroup($id,EntityManagerInterface $entityManagerInterface,ManagerRegistry $save) 
   {
    
      //we get the post
      $getpost=$entityManagerInterface->getRepository(Shareingroup::class)->find($id);

      if($getpost){
        $em=$save->getManager();
        $em->remove($getpost);
        $em->flush();
        
        return $this->redirectToRoute('app_deletepostgroup', ['id' => $getpost->getGroupid()->getId()]);
      }
      return new Response("post was deleted");
     
   }

   // here the user want to quit his group
   #[Route('/app_quitgroup/{id}', name: 'app_quitgroup')]
   public function quitgroup($id,EntityManagerInterface $entityManagerInterface,ManagerRegistry $save) 
   {  
      //we get the group entity in the addingroup table
      $getgroup_where_weare=[];
      $getgroup_where_weare=$entityManagerInterface->getRepository(Addingroup::class)->findby([
        'groupid'=>$id,
        'newmember'=>$this->getUser()
      ]);
      if($getgroup_where_weare){
        foreach($getgroup_where_weare as $goodies)
        $em=$save->getManager();
        $em->remove($goodies);
        $em->flush();
        return $this->redirectToRoute('app_usergroup');
      
      }
      return new Response("you quit the group");
     
   }

    // here the creator want to delete the group
    #[Route('/deletegroup/{id}', name: 'app_delete_group')]
    public function deletegroup($id,EntityManagerInterface $entityManagerInterface,ManagerRegistry $save) 
    {  
       //we get the group entity in the addingroup table
       $getgroup=[];
       $getgroup=$entityManagerInterface->getRepository(Groups::class)->findby([
         'id'=>$id,
       ]);
       if($getgroup){
         foreach($getgroup as $goodies)
         $em=$save->getManager();
         $em->remove($goodies);
         $em->flush();
         return $this->redirectToRoute('app_allmygroups');
       
       }
       return new Response("group deleted");
      
    }

    // here we get all group members of a particula group
    #[Route('/all_group_members/{id}', name: 'app_allmembers')]
    public function getmembers($id,EntityManagerInterface $entityManagerInterface,ManagerRegistry $save) 
    {  
       //we get the member of a particular group
       $getmembers=$entityManagerInterface->getRepository(Addingroup::class)->findby([
         'groupid'=>$id,
       ]);

       //we are getting the name of the group
       $name=$entityManagerInterface->getRepository(Groups::class)->find($id);

       $session=$this->getUser();

       //here all demande not yet threated for the admin
       $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);
 
       //we are getting the user demande to know if the decision is accepted he has to see add article button and etc...
       $validedemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$session,'decision'=>'acceptée']);
 
       // here we are geting if the user got a new unread message in the nav bar
       $getunread=$entityManagerInterface->getRepository(Message::class)->findBy([
         'usertwo'=>$this->getUser(),
         'status'=>"unread",
     ]);
     
       //this code allow us to get all inquiry not responded with the status of null
       $getallinquery=$entityManagerInterface->getRepository(Contact::class)->findBy(['status'=>null]);
         return $this->render('group/all_group_members.html.twig', [
             'controller_name' => 'GroupController',
             'users'=>$session,
             'demandes'=>$demandes,
             'validedemandes'=>$validedemandes,
             'unreadmessage'=> $getunread,
             'getallinquery'=>$getallinquery,
             'members'=>$getmembers,
             'name'=>$name
         ]);
    }

    // here we get all post for a particular group
    #[Route('/all_group_post/{id}', name: 'app_gouppost')]
    public function getarticles($id,EntityManagerInterface $entityManagerInterface,ManagerRegistry $save) 
    {  
       //we get the all article of a particular group
       $getarticle=$entityManagerInterface->getRepository(Shareingroup::class)->findby([
         'groupid'=>$id,
       ], ['id'=>'DESC'], 5);

       //we are getting the name of the group
       $name=$entityManagerInterface->getRepository(Groups::class)->find($id);

       $session=$this->getUser();

       //here all demande not yet threated for the admin
       $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);
 
       //we are getting the user demande to know if the decision is accepted he has to see add article button and etc...
       $validedemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$session,'decision'=>'acceptée']);
 
       // here we are geting if the user got a new unread message in the nav bar
       $getunread=$entityManagerInterface->getRepository(Message::class)->findBy([
         'usertwo'=>$this->getUser(),
         'status'=>"unread",
     ]);
     
       //this code allow us to get all inquiry not responded with the status of null
       $getallinquery=$entityManagerInterface->getRepository(Contact::class)->findBy(['status'=>null]);
         return $this->render('group/all_group_article.html.twig', [
             'controller_name' => 'GroupController',
             'users'=>$session,
             'demandes'=>$demandes,
             'validedemandes'=>$validedemandes,
             'unreadmessage'=> $getunread,
             'getallinquery'=>$getallinquery,
             'articles'=>$getarticle,
             'name'=>$name
         ]);
    }

   //here we edit the post group
   #[Route('/editpostgroup/{id}', name: 'app_editpostgroup')]
   public function edit($id,ManagerRegistry $save,request $request,Shareingroup $shareingroup,EntityManagerInterface $entityManager,SluggerInterface $slugger):response
   {
       $users=$this->getUser();
       //this cose is to fetch and count all demande not yet accepted by the admin
       $demandes=$entityManager->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);
       
       // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
       $validedemandes=$entityManager->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);

        //we get the post 
       $getpost=$entityManager->getRepository(Shareingroup::class)->find($id);
       
       //getting the name of the group to create a folder where the poste image will be saved 
       $nameofgroup=$getpost->getGroupid()->getGroupname();

       $getunread=$entityManager->getRepository(Message::class)->findBy([
        'usertwo'=>$this->getUser(),
        'status'=>"unread",
    ]);
    
      //this code allow us to get all inquiry not responded with the status of null
      $getallinquery=$entityManager->getRepository(Contact::class)->findBy(['status'=>null]);

       $form = $this->createForm(ShareingroupcontentType::class, $getpost);
       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {
           /** @var UploadedFile $pictures */
           $file = $form->get('image')->getData();
           $getpost->setDate(new \DateTime())
                    ->setPosterownerid($users);

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
           
        }
            $em=$save->getManager();
            $em->persist($getpost);
            $em->flush();
          
            return $this->redirectToRoute('app_editpostgroup', ['id' => $getpost->getId()]);
       }

       return $this->render('group/editpost.html.twig', [
           'users'=>$users,
           'demandes'=>$demandes,
           'validedemandes'=>$validedemandes,
           'form'=>$form->createView(),
           'unreadmessage'=> $getunread,
           'getallinquery'=>$getallinquery,
           'thepost'=>$getpost,
           'share'=>$shareingroup
       ]);
   }

   // here we are liking a post group
   #[Route('/likepostgroup/{id}', name: 'app_likepostgroup')]
   public function likepostgroup($id,EntityManagerInterface $entityManagerInterface,LikepostgroupRepository $likepostgroupRepository, ManagerRegistry $save) 
   {
      
    $getpostingroup=$entityManagerInterface->getRepository(Shareingroup::class)->find($id);
    if (!$this->getUser()) {
        return $this->json(['error' => 'User not logged in.'], 403);
    }

    if ($getpostingroup->isLikeagrouppost($this->getUser())) {

       $likegrouppost=$likepostgroupRepository->findOneBy([
        'postingroupid'=>$getpostingroup,
        'userid'=>$this->getUser()
    ]);
    $em=$save->getManager();
    $em->remove($likegrouppost);
    $em->flush();
      return $this->json(
        ['code'=>200,
               'message'=>'you have already liked this post',
               'likegrouppost'=>$likepostgroupRepository->count([
               'postingroupid'=>$getpostingroup
        ],200)
              ],200); 
    }
    $likegrouppost=new Likepostgroup();
    $likegrouppost->setUserid($this->getUser())
                  ->setPostingroupid($getpostingroup)
                  ->setDate(new \DateTime());
                $em=$save->getManager();
                $em->persist($likegrouppost);
                $em->flush();
        return $this->json(
            ['code'=>200,
                   'message'=>'like ajouté',
                   'likegrouppost'=>$likepostgroupRepository->count([
                   'postingroupid'=>$getpostingroup
            ],200)
                  ],200); 
   }

   //here we comment a post in a particular group
   #[Route('/commentgrouppost/{id}', name: 'app_commentgrouppost')]
   public function commentgrouppost($id,Request $request,CommentgrouppostRepository $commentgrouppostRepository,EntityManagerInterface $entityManagerInterface,LikepostgroupRepository $likepostgroupRepository, ManagerRegistry $save) 
   {
      $getpost=$entityManagerInterface->getRepository(Shareingroup::class)->find($id);
       
      $users=$this->getUser();
      //here all demande not yet threated for the admin
      $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);

      //we are getting the user demande to know if the decision is accepted he has to see add article button and etc...
      $validedemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$users,'decision'=>'acceptée']);

      // here we are geting if the user got a new unread message in the nav bar
      $getunread=$entityManagerInterface->getRepository(Message::class)->findBy([
        'usertwo'=>$this->getUser(),
        'status'=>"unread",
    ]);
    //we get allcomment of the post
    $commentaireall=$entityManagerInterface->getRepository(Commentgrouppost::class)->findBy(['postgroupid'=>$getpost],['id'=>'DESC']);

    // we get the id of the group in the shareingroup
    $getgroupid=$getpost->getGroupid();

    //we get the owner of the post in aparticular group
    $ownerpost=$getpost->getPosterownerid();

     
    //here we insert in th check table 
   
    // we get all post of this group
    $getgrouppostall=$entityManagerInterface->getRepository(Shareingroup::class)->findBy(['groupid'=>$getgroupid],['id'=>'DESC']);
 
    // here we are adding a comment in a particular comment
    $comment=new Commentgrouppost();
    $formcomment = $this->createForm(CommentgrouppostType::class,$comment);
    $formcomment->handleRequest($request);
        if ($formcomment->isSubmitted() && $formcomment->isValid()) {
            $em=$save->getManager();
            $comment->setDate(new \DateTime())
                    ->setUserid($users)
                    ->setPostgroupid($getpost);
           
            $em->persist($comment);
            $em->flush();
            $comment=$entityManagerInterface->getRepository(Commentgrouppost::class)->findBy(['postgroupid'=>$getpost],['id'=>'DESC']);
            //we get the owner of the post in aparticular group
            $owner=$getpost->getPosterownerid();
            return $this->render('group/resultajax.html.twig',[
                    'comments'=>$comment,
                    'owner'=>$owner
                ]);
              
            }
            else {
                $errors=[];
                foreach ($formcomment->getErrors(true) as $error) {
                    $errors[]=$error->getMessage();
                    return new JsonResponse(['status'=>'error','errors'=>$errors],400);
                }
            }
    
      //this code allow us to get all inquiry not responded with the status of null
      $getallinquery=$entityManagerInterface->getRepository(Contact::class)->findBy(['status'=>null]);

      return $this->render('group/commentpostgroup.html.twig', [
        'users'=>$users,
        'form'=>$formcomment,
        'demandes'=>$demandes,
        'validedemandes'=>$validedemandes,
        'unreadmessage'=> $getunread,
        'getallinquery'=>$getallinquery,
        'postgroup'=>$getpost,
        'commentaires'=>$commentaireall,
        'getgrouppostall'=>$getgrouppostall,
        'ownerpost'=>$ownerpost,
    ]);
   
   }

   // the admin is votting or choosing the comment as a good idea
   #[Route('/choosen/{id}', name: 'app_choosen')]
   public function choosen($id,EntityManagerInterface $entityManager,ManagerRegistry $save,VotecommentRepository $votecomment_repository): Response
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

   // we get the comment
   $getcomment=$entityManager->getRepository(Commentgrouppost::class)->find($id);

    if ($getcomment->isVotedby($session)) {
      $vote=$votecomment_repository->findOneBy([
       'commentpostingroupid'=>$getcomment,
        'userid'=>$this->getUser()
    ]);
    $em=$save->getManager();
    $em->remove($vote);
    $em->flush();
      return $this->json(
        ['code'=>200,
               'message'=>'this idea will be voted if it suits the admin or post interest',
               'vote'=>$votecomment_repository->count([
               'commentpostingroupid'=>$getcomment,
        ],200)
              ],200); 
    }
    $vote= new Votecomment();
    $vote->setCommentpostingroupid($getcomment)
                  ->setUserid($session)
                  ->setDate(new \DateTime());
                $em=$save->getManager();
                $em->persist($vote);
                $em->flush();
               return $this->json(
            ['code'=>200,
                   'message'=>'this idea is voted as the best idea of this article verry good idea',
                   'vote'=>$votecomment_repository->count([
                   'commentpostingroupid'=>$getcomment
            ],200)
                  ],200); 
   
     //things are comming soon more functionalities and methodes
   }

}
