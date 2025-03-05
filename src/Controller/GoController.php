<?php

namespace App\Controller;

use App\Entity\User;
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
        return $this->render('go/index.html.twig', [
            'users' => $users,
        ]);
    }
}
