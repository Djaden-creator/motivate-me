<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Motivateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class LandingController extends AbstractController
{
    #[Route('/', name: 'app_landing')]
    public function index(EntityManagerInterface $entityManagerInterface): Response
    {
        
        $online=$this->getUser();
        if($online)
        {
            $users=$entityManagerInterface->getRepository(User::class)->find($online);
            $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);
            return $this->render('landing/index.html.twig',[
                'users'=>$users,
                'demandes'=>$demandes,
            ] );
        }       
        return $this->render('landing/index.html.twig' );
    }
}
