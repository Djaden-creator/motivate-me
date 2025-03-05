<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WeldoneGologinController extends AbstractController
{
    #[Route('/weldone/gologin', name: 'app_weldone_gologin')]
    public function index(): Response
    {
        return $this->render('weldone_gologin/index.html.twig', [
            'controller_name' => 'WeldoneGologinController',
        ]);
    }
}
