<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Motivateur;
use App\Form\ArticleformType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use function PHPUnit\Framework\throwException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class NewArticleController extends AbstractController
{
    #[Route('/new/article', name: 'app_new_article')]
    public function index(Request $request,SluggerInterface $slugger,Security $security,ManagerRegistry $save,EntityManagerInterface $entityManagerInterface): Response
    {

        $user = $security->getUser()->getUserIdentifier();
        $users=$this->getUser();
        $userforuser = $entityManagerInterface->getRepository(User::class)->find($users);

        //this cose is to fetch and count all demande not yet accepted by the admin
        $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);
        
        // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
        $validedemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);

        $article = new Article();
        $form = $this->createForm(ArticleformType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pictures */
            $pictures = $form->get('pictures')->getData();
            $article->setCreateAt(new \DateTimeImmutable())
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
                $article->setPictures($newFilename);
            }
             $em=$save->getManager();
             $em->persist($article);
             $em->flush();
            // ... persist the $product variable or any other work

            $this->addFlash('success','Article created successfully');
            return $this->redirectToRoute('app_new_article');
        }

        return $this->render('new_article/index.html.twig', [
            'newform' => $form,
            'users'=>$userforuser,
            'demandes'=>$demandes,
            'validedemandes'=>$validedemandes,
        ]);
    }
}
