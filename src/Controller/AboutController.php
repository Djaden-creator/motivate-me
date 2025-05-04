<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Contact;
use App\Entity\Message;
use App\Entity\Motivateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AboutController extends AbstractController
{
    #[Route('/about', name: 'app_about')]
    public function index(EntityManagerInterface $entityManagerInterface): Response
    {

        $session=$this->getUser();
        if ($session) {
            $users=$entityManagerInterface->getRepository(User::class)->find($this->getUser());
            $getunread=$entityManagerInterface->getRepository(Message::class)->findBy([
                'usertwo'=>$this->getUser(),
                'status'=>"unread",
              ]);
       
            //this code allow us to get all inquiry not responded with the status of null
            $getallinquery=$entityManagerInterface->getRepository(Contact::class)->findBy(['status'=>null]);
            $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);
            $validedemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$session,'decision'=>'acceptée']);
            return $this->render('about/index.html.twig', [
                'users' => $users,
                'demandes'=>$demandes,
                'validedemandes'=>$validedemandes, 
                'unreadmessage'=> $getunread, // on ajoute cette ligne pour afficher les demandes validées par l'utilisateur courant  // 
                'getallinquery'=>$getallinquery // on ajoute cette ligne pour afficher les demandes non répondues par l'utilisateur courant  //
            ]);
        }
        else {
            return $this->render('about/index.html.twig');
        }
       
    }
}
