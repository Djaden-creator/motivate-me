<?php

namespace App\Controller;

use App\Entity\User;
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
        $users=$entityManagerInterface->getRepository(User::class)->find($this->getUser());
        $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);
        $validedemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$session,'decision'=>'acceptée']);
        return $this->render('about/index.html.twig', [
            'users' => $users,
            'demandes'=>$demandes,
            'validedemandes'=>$validedemandes,  // on ajoute cette ligne pour afficher les demandes validées par l'utilisateur courant  // 
        ]);
    }
}
