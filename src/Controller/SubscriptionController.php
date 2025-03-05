<?php

namespace App\Controller;

use App\Entity\Subcart;
use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SubscriptionController extends AbstractController
{
    #[Route('/subscription/{id}', name: 'app_subscription')]
    public function cart($id, EntityManagerInterface $em): Response
    {
        $user=$this->getUser();
        $subs=$em->getRepository(Subscription::class)->find($id);

        // count the cart where my id finds

        if(isset($user)){
            $cartentry=new Subcart();
            $cartentry->setUser($user)
                  ->setSubscriptionid($subs)
                  ->setCreatedat(new \DateTime());

                  $em->persist($cartentry);
                  $em->flush();
                  $this->addFlash('success','vous avez un item dans votre card');
                  return $this->redirectToroute('app_abonnement');
        }
    }


    
}
