<?php
namespace App\Controller;
use App\Entity\User;
use App\Form\WelcomeAddreligionType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class WelcomeAddreligionController extends AbstractController
{
#[Route('/welcome/addreligion/{id}', name: 'app_welcome_addreligion')]
public function index(Request $request,SluggerInterface $slugger,ManagerRegistry $save,EntityManagerInterface $em,$id): Response
  {
        
        $users = $em->getRepository(User::class)->find($id);
        
        $form = $this->createForm(WelcomeAddreligionType::class, $users);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pictures */
            $pictures = $form->get('picture')->getData();
            $religion = $form->get('religion')->getData();
            
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($pictures) {
                $originalFilename = pathinfo($pictures->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictures->guessExtension();
                $directory=$this->getParameter('userpicture');
                
                $destinationfile=''.$directory.'/'.$users->getEmail().'';
                $target_file=$directory.basename($newFilename);
                
                // Move the file to the directory where brochures are stored
                try {
                   
                    if (file_exists($directory)) {
                        $pictures->move($directory,$newFilename);

                        if (!file_exists ($destinationfile)) {
                            mkdir($destinationfile,0777,true);
                        } 
                        if (file_exists($destinationfile)) {
                            $source='userpicture/'.$newFilename;
                            $to='userpicture/'.$users->getEmail().'/'.$newFilename;
                            copy($source,$to);
                        }
                    }
                       
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                
                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
             $users->setPicture($newFilename)
                   ->setReligion($religion);
                
            }
            $em->flush();
            // ... persist the $product variable or any other work
            return $this->redirectToRoute('app_go');
        }
        return $this->render('welcome_addreligion/index.html.twig', [
            'form' => $form->createView(),
            'users'=>$users
        ]);
    }
}