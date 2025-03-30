<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Contact;
use App\Entity\Message;
use App\Form\ContactType;
use App\Entity\Motivateur;
use App\Form\ResponseinqueryType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use function PHPUnit\Framework\isEmpty;

final class ContactController extends AbstractController
{
    // here we are showing all inquiry and demande from the user and admin to view and response it
    #[Route('/inquiry', name: 'app_inquiry')]
    public function index(EntityManagerInterface $entityManagerInterface): Response
    {

      $session=$this->getUser();
      //here all demande not yet threated for the admin
      $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);

      //we are getting the user demande to know if the decision is accepted he has to see add article button and etc...
      $validedemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$session,'decision'=>'acceptée']);

       //this code allow us to get all inquiry not responded with the status of null
       $getallinquery=$entityManagerInterface->getRepository(Contact::class)->findBy(['status'=>null]);

      // here we are geting if the user got a new unread message in the nav bar
      $getunread=$entityManagerInterface->getRepository(Message::class)->findBy([
        'usertwo'=>$this->getUser(),
        'status'=>"unread",
    ]);
    
        // here we get all inquiry sent by user with statu null
        $getinquiry=$entityManagerInterface->getRepository(Contact::class)->findBy(['status'=>null]);
        return $this->render('contact/index.html.twig', [
            'getinquiry' => $getinquiry,
            'demandes'=>$demandes,
            'validedemandes'=>$validedemandes,
            'unreadmessage'=>$getunread,
            'users'=>$session,
            'getallinquery'=>$getallinquery,
        ]);
    }


    //here to response and view the inquiry or demande

    #[Route('/response/{id}', name: 'app_response')]
    public function response($id,EntityManagerInterface $entityManagerInterface, ManagerRegistry $save,Request $request): Response
    {

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
     // here we get all inquiry sent by user with statu null
     $getinquiry=$entityManagerInterface->getRepository(Contact::class)->find($id);

     //this code allow us to get all inquiry not responded with the status of null
     $getallinquery=$entityManagerInterface->getRepository(Contact::class)->findBy(['status'=>null]);
    
        $form = $this->createForm(ResponseinqueryType::class, $getinquiry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $stat = $form->get('status')->getData();
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
             if ($stat) {
                $getinquiry->setStatus($stat);
             }
             $em=$save->getManager();
             $em->persist($getinquiry);
             $em->flush();
            
         return $this->redirectToRoute('app_response',['id'=>$id]);
        }
       
        return $this->render('contact/reply.html.twig', [
            'getinquiry' => $getinquiry,
            'demandes'=>$demandes,
            'validedemandes'=>$validedemandes,
            'unreadmessage'=>$getunread,
            'users'=>$session,
            'form' => $form->createView(),
            'getallinquery'=>$getallinquery,
        ]);
    }

    // here the user is contacting us for his demande or for his motivator account
    #[Route('/contact', name: 'contactus')]
    public function contactus(Request $request,EntityManagerInterface $entityManagerInterface,ManagerRegistry $save): Response
    {
        
        $users=$this->getUser();
        $userforuser = $entityManagerInterface->getRepository(User::class)->find($users);
        $mymail=$userforuser->getEmail();
        $myname=$userforuser->getUsername();

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
        $contact= new Contact;
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $token=random_bytes(5);
            $generate=bin2hex($token);
            $suivinumber=$form->get('suivinumber')->getData();
            $purpose=$form->get('purpose')->getData();
            $description=$form->get('description')->getData();

            $contact->setUsermail($mymail)
            ->setUsername($myname)
            ->setUserid($this->getUser())
            ->setDate( new \DateTime())
            ->setContactsuivinumber($generate);
            if(empty($suivinumber && $purpose && $description)){
                return new JsonResponse([
                    'status' => 'error',
                    'errors' =>'tout le champs doivent etre remplis',
                ], 400);
                }
            $count = $entityManagerInterface->createQueryBuilder()
            ->select('COUNT(m.id)') // Count the number of rows
            ->from(Motivateur::class, 'm')
            ->where('m.demandenumbe = :suivinumber')
            ->setParameter('suivinumber', $suivinumber)
            ->getQuery()
            ->getSingleScalarResult();
            
            if ($count == 1) {
            $em=$save->getManager();
            $em->persist($contact);
            $em->flush();
            
            return new JsonResponse(['status' => 'success', 'message' => 'votre message a été bien envoyé!']);
            }
            else{
                return new JsonResponse([
                    'status' => 'error',
                    'errors' =>'desolé votre numero de suivis n\'existe pas',
                ], 400);
            }
               
            return new JsonResponse([
                'status' => 'error',
                'errors' =>$form->getErrors(true, false),
            ], 400);
       }
   
       return $this->render('contact/contact.html.twig', [
        'newform'=>$form,
        'users'=>$userforuser,
        'demandes'=>$demandes,
        'validedemandes'=>$validedemandes,
        'contactform'=>$form,
        'unreadmessage'=> $getunread,
        'getallinquery'=>$getallinquery,
    ]);
    }

}
