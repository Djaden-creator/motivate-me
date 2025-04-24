<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Contact;
use App\Entity\Message;
use App\Entity\Motivateur;
use App\Form\ArticleformType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UpdateController extends AbstractController
{
    // here we update my article a single article
    #[Route('/update/{id}', name: 'app_update')]
    public function edit(ManagerRegistry $save,request $request,$id,EntityManagerInterface $entityManager,ArticleRepository $repos,SluggerInterface $slugger,Security $security):response
    {
        $user = $security->getUser()->getUserIdentifier();
        $users=$this->getUser();
        $userofuser = $entityManager->getRepository(User::class)->find($users);
        $articles=$repos->find($id);

        //this cose is to fetch and count all demande not yet accepted by the admin
        $demandes=$entityManager->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);
        
        // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
        $validedemandes=$entityManager->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);

        $getunread=$entityManager->getRepository(Message::class)->findBy([
            'usertwo'=>$this->getUser(),
            'status'=>"unread",
        ]);
        
          //this code allow us to get all inquiry not responded with the status of null
          $getallinquery=$entityManager->getRepository(Contact::class)->findBy(['status'=>null]);
        $form = $this->createForm(ArticleformType::class, $articles);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pictures */
            $pictures = $form->get('pictures')->getData();
            $articles->setCreateAt(new \DateTimeImmutable())
                     ->setUserposter($users);

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($pictures) {
                $originalFilename = pathinfo($pictures->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictures->guessExtension();
                $directory=$this->getParameter('directory');
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
                $articles->setPictures($newFilename);
            }
             $em=$save->getManager();
             $em->persist($articles);
             $em->flush();
            // ... persist the $product variable or any other work
             $this->addFlash(
                'success',
                'votre article a été edité avec succée'
             );
             return $this->redirectToRoute('app_fichier');
        }

        return $this->render('update/index.html.twig', [
            'newform' => $form,
            'users'=>$userofuser,
            'demandes'=>$demandes,
            'validedemandes'=>$validedemandes,
            'unreadmessage'=> $getunread,
            'getallinquery'=>$getallinquery,  //this variable is used to check if user has a valid inquiry to send message or not.
        ]);
        
        
    }
}
