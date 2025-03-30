<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Contact;
use App\Entity\Motivateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GoController extends AbstractController
{
    #[Route('/go', name: 'app_go')]
    public function index(EntityManagerInterface $entityManagerInterface): Response
    {
        $users=$entityManagerInterface->getRepository(User::class)->find($this->getUser());
         // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
         $validedemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);
        
         //this code allow us to get all inquiry not responded with the status of null
        $getallinquery=$entityManagerInterface->getRepository(Contact::class)->findBy(['status'=>null]);

        return $this->render('go/index.html.twig', [
            'users' => $users,
            'validedemandes'=>$validedemandes,
            'getallinquery'=>$getallinquery,
        ]);
    }
}
