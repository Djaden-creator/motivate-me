<?php

namespace App\Controller;

use App\Entity\Following;
use App\Entity\Motivateur;
use App\Entity\User;
use App\Repository\MotivateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(EntityManagerInterface $em): Response
    {
        $users=$em->getRepository(User::class)->find($this->getUser());

        // this code is about to fetch all demande of user not yet threated by the admin new demande
        $demandes=$em->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);

        //here we are getting a user demande for a particular user
        $userdemandes=$em->getRepository(Motivateur::class)->findby(['user'=>$this->getUser()]);

        // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
        $validedemandes=$em->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);
       
         // we are getting all motivator count that this user online following
         $followingcount=$em->getRepository(Following::class)->findby(['usersessionid'=>$this->getUser()]);
        

       return $this->render('profil/index.html.twig', [
            'users' => $users,
            'demandes'=> $demandes,
            'validedemandes'=>$validedemandes,
            'userdemandes'=>$userdemandes,
            'followingcount'=> $followingcount
        ]);
    }

    #[Route('/delete_demande/{id}', name: 'app_delete_demande')]
    public function deleteDemand($id,EntityManagerInterface $entityManager,MotivateurRepository $motivateurRepository):response
    {
        $demande=$motivateurRepository->find($id);
        $entityManager ->remove($demande);
        $entityManager ->flush();
    
        return $this->redirectToroute('app_profil');
    }
}
