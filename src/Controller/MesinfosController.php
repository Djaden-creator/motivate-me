<?php

namespace App\Controller;

use Exception;
use App\Entity\User;
use App\Form\MyinfoType;
use App\Entity\Motivateur;
use App\Form\UserformType;
use App\Entity\Subscription;
use App\Form\UpdatepasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MesinfosController extends AbstractController
{
     //here we shall retrieve all user info personelle
    #[Route('/mesinfos', name: 'app_mesinfos')]
    public function index(EntityManagerInterface $em): Response
    {
        $users=$em->getRepository(User::class)->find($this->getUser());

        // this code is about to fetch all demande of user not yet threated by the admin new demande 
        $demandes=$em->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);

        // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
        $validedemandes=$em->getRepository(Motivateur::class)->findby(['user'=>$users,'decision'=>'acceptée']);
        return $this->render('mesinfos/index.html.twig', [
            'users' => $users,
            'demandes'=>$demandes,
            'validedemandes'=>$validedemandes
        ]);
    }

     //here we shall retrieve all abonnement list disponible
     #[Route('/mesinfos/abonnement', name: 'app_abonnement')]
     public function Abonnement(EntityManagerInterface $em): Response
     {
        $session=$this->getUser();

        $user = $em->getRepository(User::class)->find($session);

       // this code is about to fetch all demande of user not yet threated by the admin new demande 
       $demandes=$em->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);

       // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
       $validedemandes=$em->getRepository(Motivateur::class)->findby(['user'=>$session,'decision'=>'acceptée']);
       
       // here to get all plan of subscription in the database
        $subscriptions=$em->getRepository(Subscription::class)->findAll();

        if (! $subscriptions) {
            throw new Exception("errror on this one", 1);
            
        }
         return $this->render('mesinfos/abonnement.html.twig', [
            'subscriptions'=>$subscriptions,
            'users'=>$user,
            'demandes'=>$demandes,
            'validedemandes'=>$validedemandes 
         ]);
     }

     //here we editing the user information
     #[Route('/mesinfos/info', name: 'app_perso')]
     public function myInfo(EntityManagerInterface $em,Request $request,SluggerInterface $slugger,Security $security,ManagerRegistry $save): Response
     {
        $users=$em->getRepository(User::class)->find($this->getUser());

       // this code is about to fetch all demande of user not yet threated by the admin new demande 
       $demandes=$em->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);

       // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
       $validedemandes=$em->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);

        $form = $this->createForm(MyinfoType::class, $users);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em=$save->getManager();
            $em->persist($users);
            $em->flush();
           // ... persist the $product variable or any other work
            $this->addFlash(
               'success',
               'votre info de profil a été edité avec succée'
            );
            return $this->redirectToRoute('app_mypublication');
        }
         return $this->render('mesinfos/info.html.twig', [
             'users' => $users,
             'form' => $form,
             'demandes'=>$demandes,
             'validedemandes'=>$validedemandes
         ]);
     }

     // her are editing the password  field
     #[Route('/mesinfos/passwordupdate', name: 'app_pasword')]
     public function MyPasswordedit(UserPasswordHasherInterface $userPasswordHasherInterface,EntityManagerInterface $em,Request $request,SluggerInterface $slugger,Security $security,ManagerRegistry $save): Response
     {
        $users=$em->getRepository(User::class)->find($this->getUser());
        $formpass = $this->createForm(UpdatepasswordType::class, $users);
        // this code is about to fetch all demande of user not yet threated by the admin new demande 
       $demandes=$em->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);

        // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
        $validedemandes=$em->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);
        
        $formpass->handleRequest($request);
        if ($formpass->isSubmitted() && $formpass->isValid()) {
            $updatepassword=$formpass->get('updateme')->getData();
            $users->setPassword($userPasswordHasherInterface->hashPassword($users,$updatepassword));
            $em->persist($users);
            $em->flush();
           // ... persist the $product variable or any other work
            $this->addFlash(
               'success',
               'votre votre password a été edité avec succée'
            );
            return $this->redirectToRoute('app_mypublication');
        }
         return $this->render('mesinfos/password_update.html.twig', [
             'users' => $users,
             'formpass' => $formpass,
             'demandes'=>$demandes,
             'validedemandes'=>$validedemandes  // this is to show the add article button if the user is accepted by the admin
         ]);
     }

    // here we are editing the user profil picture
    #[Route('/mesinfos/pictureupdate', name: 'app_pictureupdate')]
    public function pictureupdate(EntityManagerInterface $em,Request $request,SluggerInterface $slugger,Security $security,ManagerRegistry $save): Response
    {
        $user = $security->getUser()->getUserIdentifier();
        $users=$em->getRepository(User::class)->find($this->getUser());

       // this code is about to fetch all demande of user not yet threated by the admin new demande 
       $demandes=$em->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);

       // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
       $validedemandes=$em->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);
        
       $form = $this->createForm(UserformType::class, $users);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pictures */
            $pictures = $form->get('pictures')->getData();
            
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($pictures) {
                $originalFilename = pathinfo($pictures->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictures->guessExtension();
                $directory=$this->getParameter('userpicture');
                $destinationfile=''.$directory.'/'.$user.'';
                $target_file=$directory.basename($newFilename);


                // Move the file to the directory where brochures are stored
                try {
                   
                    if (!file_exists ($destinationfile)) {
                        mkdir($destinationfile,0777,true);
                        
                        }
                        
                        $pictures->move($destinationfile, $newFilename);
                    
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $users->setPicture($newFilename);
            }
             $em=$save->getManager();
             $em->persist($users);
             $em->flush();
            // ... persist the $product variable or any other work
             $this->addFlash(
                'success',
                'votre photo de profil a été edité avec succée'
             );

            return $this->redirectToRoute('app_mypublication');
        }
        return $this->render('mesinfos/updatepicture.html.twig', [
            'users' => $users,
            'form' => $form,
            'demandes'=>$demandes,
            'validedemandes'=>$validedemandes  // here we pass the demande of user to the view to show it in the template if the user is accepted by the admin
        ]);
    }
}
