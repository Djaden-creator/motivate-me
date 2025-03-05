<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Commande;
use App\Entity\Motivateur;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CommandesController extends AbstractController
{
    #[Route('/commandes', name: 'app_commandes')]
    public function index(EntityManagerInterface $entityManagerInterface): Response
    {
        $user=$this->getUser();
        $users=$entityManagerInterface->getRepository(User::class)->find($this->getUser());

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
}
