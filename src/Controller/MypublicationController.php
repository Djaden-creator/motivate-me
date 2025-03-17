<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Motivateur;
use App\Entity\Sauvegarde;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MypublicationController extends AbstractController
{
    // here we get all my saved article
    #[Route('/mypublication', name: 'app_mypublication')]
    public function index(EntityManagerInterface $em): Response
    {
        $user=$this->getUser();
        $users=$em->getRepository(User::class)->find($this->getUser());
        $sauvegardearticles=$em->getRepository(Sauvegarde::class)->findBy(['user'=>$user]);

        //this cose is to fetch and count all demande not yet accepted by the admin
        $demandes=$em->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);
        
        // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
        $validedemandes=$em->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);
        
        return $this->render('mypublication/index.html.twig', [
            'sauvegardearticles' => $sauvegardearticles,
            'users' => $users,
            'demandes'=>$demandes,
            'validedemandes'=>$validedemandes,  //this variable is used to check if user has a valid demande to add article or not.
        ]);
    }

    //here we get all my poste
    #[Route('/mypostes', name: 'app_mypostes')]
    public function mypost(EntityManagerInterface $em): Response
    {
        $user=$this->getUser();
        $users=$em->getRepository(User::class)->find($this->getUser());
        $mypostes=$em->getRepository(Article::class)->findBy(['userposter'=>$user]);

        //this cose is to fetch and count all demande not yet accepted by the admin
        $demandes=$em->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);
        
        // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
        $validedemandes=$em->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);
        
        return $this->render('mypublication/poste.html.twig', [
            'mypostes' => $mypostes,
            'users' => $users,
            'demandes'=>$demandes,
            'validedemandes'=>$validedemandes,  //this variable is used to check if user has a valid demande to add article or not.
        ]);
    }
}
