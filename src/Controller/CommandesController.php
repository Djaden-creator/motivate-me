<?php

namespace App\Controller;

use Exception;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\Contact;
use App\Entity\Fichier;
use App\Entity\Message;
use App\Entity\Commande;
use App\Entity\Motivateur;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CommandesController extends AbstractController
{
    #[Route('/commandes', name: 'app_commandes')]
    public function index(EntityManagerInterface $entityManagerInterface): Response
    {
        $user=$this->getUser();
        $users=$entityManagerInterface->getRepository(User::class)->find($this->getUser());

         // this code is to get all message unread for the user
         $getunread=$entityManagerInterface->getRepository(Message::class)->findBy([
            'usertwo'=>$this->getUser(),
            'status'=>"unread",
          ]);


          //this code allow us to get all inquiry not responded with the status of null
       $getallinquery=$entityManagerInterface->getRepository(Contact::class)->findBy(['status'=>null]);
       
        // this code is about to fetch all demande of user not yet threated by the admin new demande 
       $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);

       // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
       $validedemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);
        
       $getcomment=$entityManagerInterface->getRepository(Commande::class)->findBy(['user'=>$user]);
       
       return $this->render('commandes/index.html.twig', [
            'commandes' => $getcomment,
            'users'=> $user,
            'device'=>$_ENV['STRIPE_CURRENCY'],
            'demandes'=>$demandes,
            'validedemandes'=>$validedemandes,
            'unreadmessage'=>$getunread,
            'getallinquery'=>$getallinquery,
        ]);
    }

    // here we delete all command for a particuler user
    #[Route('/deletecommande/{id}', name: 'app_delete_allcommande')]
    public function deleteallcommande($id,EntityManagerInterface $entityManagerInterface):response
    {
        $users=$entityManagerInterface->getRepository(User::class)->find($this->getUser());
        // this code is about to fetch all demande of user not yet threated by the admin new demande 
       $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);

       // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
       $validedemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);
        
       $commande=$entityManagerInterface->getRepository(Commande::class)->find($id);
        if($commande)
        {
            $entityManagerInterface->remove($commande);
            $entityManagerInterface->flush();
            return $this->redirectToRoute('app_commandes');
        }
        return $this->redirectToRoute('app_commandes');

    }

    // here we delete all command for a particuler user
    #[Route('/download/{id}', name: 'app_download')]
    public function download($id,EntityManagerInterface $entityManagerInterface):response
    {
        try {
            $file=$entityManagerInterface->getRepository(Article::class)->find($id);
            $getfichier=$file->getFichier();
            $path = $this->getParameter('fichier').'/'.$getfichier;
            if (!file_exists($path)) {
               throw $this->createNotFoundException("oups the file does not exist");
               
            }

            // create the response
            // $response = new Response();
            // $response->headers->set('Content-Type', 'application/force-download');
            // $response->headers->set('Content-Disposition', $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $getfichier));
            // $response->headers->set('Content-Transfer-Encoding', 'binary');
            // $response->headers->set('Content-Length', filesize($path));
            // $response->setContent(file_get_contents($path));
            // return $response;
            $response=new BinaryFileResponse($path);
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT,$getfichier);
            return $response;
        } catch (Exception $e) {
            throw $this->createNotFoundException("oups the file does not exist");
        }

    }
}
