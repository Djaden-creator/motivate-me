<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Contact;
use App\Entity\Message;
use App\Form\ContactType;
use App\Entity\Motivateur;
use App\Form\MotivateurType;
use App\Entity\Generatorcode;
use App\Form\GeneratorcodeType;
use App\Form\ValidedemandeType;
use App\Form\GivesellercodeType;
use App\Form\ValidatenumberType;
use App\Form\GivesellernumberType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class MotivatorController extends AbstractController
{

    // to send the demande of motivator
    #[Route('/motivator', name: 'app_motivator')]
    public function index(Security $security,Request $request,EntityManagerInterface $entityManagerInterface,SluggerInterface $sluggerInterface,ManagerRegistry $managerRegistry): Response
    {
        $getemail=$security->getUser()->getUserIdentifier();
        $users=$this->getUser();
        $userforuser = $entityManagerInterface->getRepository(User::class)->find($users);

        // this code is to get all message unread for the user
        $getunread=$entityManagerInterface->getRepository(Message::class)->findBy([
            'usertwo'=>$this->getUser(),
            'status'=>"unread",
          ]);

        //this code allow us to get all inquiry not responded with the status of null
        $getallinquery=$entityManagerInterface->getRepository(Contact::class)->findBy(['status'=>null]);

        //here all demande not yet threated for the admin with a status of traitement encore in the base.html.twig nav bar
        $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);
        
        //here if the user has already a demande he will not see the form of submiting a demande for motivatuer
        $demandeform=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$this->getUser()]);
         
        // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
        $validedemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$users,'decision'=>'acceptée']);
        
        $motivator = new Motivateur();
        $form = $this->createForm(MotivateurType::class, $motivator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pictures */
            // getting all form data
            $pictures=$form->get('picture')->getData();
            // here to get the unique demande number
            $token=random_bytes(4);
            $generate=bin2hex($token);
            $motivator->setDate(new \DateTimeImmutable())
                      ->setUser($users)
                      ->setEmail($getemail)
                      ->setDecision('traitement encours...')
                      ->setMotif('none')
                      ->setDemandenumbe($generate);

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($pictures) {
                $originalFilename = pathinfo($pictures->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $sluggerInterface->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictures->guessExtension();
                $directory=$this->getParameter('motivateur');
                $destinationfile=''.$directory.'/'.$getemail.'';
                $target_file=$directory.basename($newFilename);
                // Move the file to the directory where brochures are stored
                try {
                    if (file_exists($directory)) {
                        $pictures->move($directory,$newFilename);

                        if (!file_exists ($destinationfile)) {
                            mkdir($destinationfile,0777,true);
                        } 
                        if (file_exists($destinationfile)) {
                            $source='motivator/'.$newFilename;
                            $to='motivator/'.$userforuser->getEmail().'/'.$newFilename;
                            copy($source,$to);
                        }
                    }
                    if (!file_exists ($destinationfile)) {
                        mkdir($destinationfile,0777,true);
                        }
                        $pictures->move($destinationfile, $newFilename);
                    
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $motivator->setPicture($newFilename);
            }
             $em=$managerRegistry->getManager();
             $em->persist($motivator);
             $em->flush();
             return $this->redirectToRoute('app_motivator');
            // ... persist the $product variable or any other work
        }
        return $this->render('motivator/index.html.twig', [
            'newform' => $form,
            'users'=>$userforuser,
            'demandeform'=>$demandeform,
            'validedemandes'=>$validedemandes,
            'demandes'=>$demandes,
            'unreadmessage'=> $getunread,
            'getallinquery'=>$getallinquery
        ]);
    }

    // to get all user demande by the admin all new demande non traité
    #[Route('/user_demande', name: 'app_user_demande')]
    public function userdemande(EntityManagerInterface $entityManagerInterface): Response
    {
        
        $users=$this->getUser();
        $userforuser = $entityManagerInterface->getRepository(User::class)->find($users);

        // this code is to get all message unread for the user
        $getunread=$entityManagerInterface->getRepository(Message::class)->findBy([
            'usertwo'=>$this->getUser(),
            'status'=>"unread",
          ]);

        // this code is to get all demande not yet validate by the admin status== traitement encours
        $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);
        
         //this code allow us to get all inquiry not responded with the status of null
         $getallinquery=$entityManagerInterface->getRepository(Contact::class)->findBy(['status'=>null]);

         // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
         $validedemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$users,'decision'=>'acceptée']);
        
         return $this->render('motivator/alldemande.html.twig', [
            'users'=>$userforuser,
            'demandes'=>$demandes,
            'validedemandes'=>$validedemandes,
            'unreadmessage'=> $getunread,
            'getallinquery'=>$getallinquery
        ]);
    }

    // here we are updating and giving a user a seller number. we are updating the motivateur table 
    #[Route('/one_user_demande/{id}', name: 'app_single_user_demande')]
    public function singledemande($id,EntityManagerInterface $entityManagerInterface,Request $request,ManagerRegistry $save): Response
    {
        
        $users=$this->getUser();
        $userforuser = $entityManagerInterface->getRepository(User::class)->find($users);
        $processing=$entityManagerInterface->getRepository(Motivateur::class)->find($id);

         // this code is to get all message unread for the user
         $getunread=$entityManagerInterface->getRepository(Message::class)->findBy([
            'usertwo'=>$this->getUser(),
            'status'=>"unread",
          ]);

           //this code allow us to get all inquiry not responded with the status of null
        $getallinquery=$entityManagerInterface->getRepository(Contact::class)->findBy(['status'=>null]);

        // this code is to get all demande not yet validate by the admin status== traitement encours
        $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);

       // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
        $validedemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$users,'decision'=>'acceptée']);
       
       //here we are edding the user info in motivateur table  for decision
        $form = $this->createForm(GivesellercodeType::class, $processing);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $decision = $form->get('decision')->getData();
            $code=$form->get('codeseller')->getData();
            $update=$entityManagerInterface->getRepository(Generatorcode::class)->find($code);
            //here we are updating the code seller,decision and motif in the motivator table
            $em=$save->getManager();
            $em->persist($processing);
            $em->flush();
                //then after updating the motif,decision and code seller now  here we are updating the status= disponible to status= used.
                // used code will not show up in the form when we are giving the code seller to a motivator        
                if($update){
                    $update->setStatus('used');    
            // Persist and flush the changes
                $entityManagerInterface->persist($update);
                 $entityManagerInterface->flush();
                }
                return $this->redirectToRoute('app_user_demande');
            };
                                             
           return $this->render('motivator/single_demande.html.twig', [
            'users'=>$userforuser,
            'demandes'=>$demandes,
            'form'=>$form,
            'validedemandes'=>$validedemandes,
            'processing'=>$processing,
            'unreadmessage'=> $getunread,
            'getallinquery'=>$getallinquery
        ]);
    }
    // here to create a seller number of user
    // to get  a single demande by id
    #[Route('/generator_seller', name: 'app_generator')]
    public function generator(EntityManagerInterface $entityManagerInterface,Request $request,ManagerRegistry $save): Response
    {
        $users=$this->getUser();
        $userforuser = $entityManagerInterface->getRepository(User::class)->find($users);
        $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'none']);

         // this code is to get all demande not yet validate by the admin status== traitement encours
         $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);


          //this code allow us to get all inquiry not responded with the status of null
        $getallinquery=$entityManagerInterface->getRepository(Contact::class)->findBy(['status'=>null]);

          // this code is to get all message unread for the user
          $getunread=$entityManagerInterface->getRepository(Message::class)->findBy([
            'usertwo'=>$this->getUser(),
            'status'=>"unread",
          ]);

         // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
         $validedemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$users,'decision'=>'acceptée']);
        $code = new Generatorcode();
        $form = $this->createForm(GeneratorcodeType::class, $code);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $codehere=$form->get('codenumber')->getData();
            $code->setDate(new \DateTimeImmutable())
            ->setStatus('disponible');
           
            if(empty($codehere)){
               return new JsonResponse([
                   'status' => 'error',
                   'errors' =>'ce champ ne dois pas etre vide',
               ], 400);
   
            }elseif ($longreference = strlen($codehere)) {
                # code... $longreference = strlen($reference);
               if ($longreference > 6) {
                return new JsonResponse([
                   'status' => 'error',
                   'errors' => 'le motivator code doit faire moins de 7 caractères',
                ], 400);
               } else {
        }
            }
           $code->setCodenumber("Mot-".uniqid($codehere));
           $em=$save->getManager();
           $em->persist($code);
           $em->flush();
           return new JsonResponse(['status' => 'success', 'message' => 'motivator code bien enregistré']);
       }
        
        return $this->render('motivator/generator_seller_number.html.twig', [
            'users'=>$userforuser,
            'demandes'=>$demandes,
            'form'=>$form,
            'demandes'=>$demandes,
            'validedemandes'=>$validedemandes,
            'unreadmessage'=> $getunread,
            'getallinquery'=>$getallinquery
        
        ]);
    }
}

