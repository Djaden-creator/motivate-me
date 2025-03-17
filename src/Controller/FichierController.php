<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Form\FichierType;
use App\Entity\Motivateur;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class FichierController extends AbstractController
{
    // here we are getting the last article created by the motivator
    #[Route('/fichier', name: 'app_fichier')]
    public function index(EntityManagerInterface $entityManagerInterface): Response
    {

        //this cose is to fetch and count all demande not yet accepted by the admin
        $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);
        
        // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
        $validedemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);

        //getting the connected user 
        $userforuser = $entityManagerInterface->getRepository(User::class)->find($this->getUser());
         
        //we are getting the last posted article of this user that has to be linked with the file
         $getarticles=$entityManagerInterface->getRepository(Article::class)->findBy(['userposter'=>$this->getUser()],['id'=>'DESC'],1);
        return $this->render('fichier/index.html.twig', [
            'demandes'=>$demandes,
            'validedemandes'=>$validedemandes,
            'users'=>$userforuser,
            'getarticles'=>$getarticles
        ]);
    }

    //here to upload or link a data to an article like pdf et..
    #[Route('/upload/{id}', name: 'app_upload')]
    public function uploadfile(Request $request,Article $article,SluggerInterface $slugger,Security $security,ManagerRegistry $save, EntityManagerInterface $entityManagerInterface): Response
    {

        $user = $security->getUser()->getUserIdentifier();
        $users=$this->getUser();

        //getting the connected user
        $userforuser = $entityManagerInterface->getRepository(User::class)->find($users);
        $article->getId();
        //we are getting the last posted article of this user that has to be linked with the file
        $getarticle=$entityManagerInterface->getRepository(Article::class)->findBy(['userposter'=>$users],['id'=>'DESC'],1);
        //this cose is to fetch and count all demande not yet accepted by the admin
        $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);
        
        // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
        $validedemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);

        $form = $this->createForm(FichierType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $data */
            $file = $form->get('fichier')->getData();
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                $directory=$this->getParameter('fichier');
                $destinationfile=''.$directory.'/'.$user.'';
                $target_file=$directory.basename($newFilename);

                // Move the file to the directory where brochures are stored
                try {
                   
                    if (file_exists($directory)) {
                        $file->move($directory,$newFilename);

                        if (!file_exists ($destinationfile)) {
                            mkdir($destinationfile,0777,true);
                        } 
                        if (file_exists($destinationfile)) {
                            $source='fichier/'.$newFilename;
                            $to='fichier/'.$userforuser->getEmail().'/'.$newFilename;
                            copy($source,$to);
                        }
                    }
                    if (!file_exists ($destinationfile)) {
                        mkdir($destinationfile,0777,true);
                        }
                        $file->move($destinationfile, $newFilename);
                    
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $article->setFichier($newFilename);
            }
             $em=$save->getManager();
             $em->persist($article);
             $em->flush();
            // ... persist the $product variable or any other work

            return $this->redirectToRoute('app_blog');
        }

        return $this->render('fichier/form.html.twig', [
            'article'=>$article,
            'users'=>$userforuser,
            'newform' => $form,
            'getarticle'=>$getarticle,
            'demandes'=>$demandes,
            'validedemandes'=>$validedemandes,
        ]);
    }
}
