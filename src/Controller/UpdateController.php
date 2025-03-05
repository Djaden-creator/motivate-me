<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Form\ArticleformType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UpdateController extends AbstractController
{
    #[Route('/update/{id}', name: 'app_update')]
    public function edit(ManagerRegistry $save,request $request,$id,EntityManagerInterface $entityManager,ArticleRepository $repos,SluggerInterface $slugger,Security $security):response
    {
        $user = $security->getUser()->getUserIdentifier();
        $users=$this->getUser();
        $userofuser = $entityManager->getRepository(User::class)->find($users);
        $articles=$repos->find($id);
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
        }

        return $this->render('update/index.html.twig', [
            'newform' => $form,
            'users'=>$userofuser
        ]);
        
        
    }
}
