<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\Religion;
use App\Entity\Motivateur;
use App\Form\ReligionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class ReligionController extends AbstractController
{
    #[Route('/religion', name: 'app_religion')]
    public function index(Request $request,SluggerInterface $slugger,Security $security,EntityManagerInterface $entityManagerInterface): Response
    {

        $user = $security->getUser()->getUserIdentifier();
        $users=$entityManagerInterface->getRepository(User::class)->find($this->getUser());
       // this code is about to fetch all demande of user not yet threated by the admin new demande 
        $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);

       // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
        $validedemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);
        
        $religion= new Religion();
        $form = $this->createForm(ReligionType::class, $religion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pictures */
            $pictures = $form->get('picture')->getData();
            $religion->setDate(new \DateTimeImmutable())
                     ->setUser($this->getUser());
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($pictures) {
                $originalFilename = pathinfo($pictures->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictures->guessExtension();
                $directory=$this->getParameter('religion');
                $destinationfile=''.$directory.'/'.$user.'';
                $target_file=$directory.basename($newFilename);

                // Move the file to the directory where brochures are stored
                try {
                   
                    if (!file_exists ($destinationfile)) {
                        mkdir($destinationfile,0777,true);
                        
                        }
                        
                        $pictures->move($destinationfile, $newFilename);
                    
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $religion->setPicture($newFilename);
            }
             $entityManagerInterface->persist($religion);
             $entityManagerInterface->flush();
            // ... persist the $product variable or any other work

            $this->addFlash('success','Religion created successfully');
            return $this->redirectToRoute('app_religion');
        }

        return $this->render('religion/index.html.twig', [
            'religionform' => $form,
            'users'=>$users,
            'demandes'=>$demandes,
            'validedemandes'=>$validedemandes  // this variable is to check if the user has a valid demande to add article  // or not  // the demande of user is accepted he has to see the add article button etc... to add article  // and he can't add article if he doesn't have a valid demande of user.  // this variable is to check if the user has a valid demande to add article  // or not
        ]);
    }

    #[Route('/religion/{id}', name: 'app_getreligion')]
    public function showonereligion($id,EntityManagerInterface $entityManagerInterface): Response
    {
        $users=$entityManagerInterface->getRepository(User::class)->find($this->getUser());
        $religion = $entityManagerInterface->getRepository(Religion::class)->find($id);
         // this code is about to fetch all demande of user not yet threated by the admin new demande 
        $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);

        // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
        $validedemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);      
           
        if (!$religion) {
                throw $this->createNotFoundException(
                    'No product found for id '.$id
                );
            }
            return $this->render('religion/lire.html.twig', [
                'religion'=> $religion,
                'users'=>$users,
                'demandes'=>$demandes,
                'validedemandes'=>$validedemandes  // this variable is to check if the user has a valid demande to add article  // or not  // the demande of user is accepted he has to see the add article button etc... to add article  // and he can't add article if he doesn't have a valid demande of user.  // this variable is to check if the user has a valid demande to add article  // or not                              
            ]);
        }
}
