<?php
namespace App\Controller;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\Contact;
use App\Entity\Message;
use App\Entity\Motivateur;
use App\Repository\UserRepository;
use App\Form\WelcomeAddreligionType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class WelcomeAddreligionController extends AbstractController
{
#[Route('/welcome/addreligion/{id}', name: 'app_welcome_addreligion')]
public function index(Request $request,SluggerInterface $slugger,ManagerRegistry $save,EntityManagerInterface $em,$id): Response
  {
        
        $users = $em->getRepository(User::class)->find($id);
        
        // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
        $validedemandes=$em->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);

        $getarticle=$em->getRepository(Article::class)->findBy(['userposter'=>$users],['id'=>'DESC'],1);
        //this cose is to fetch and count all demande not yet accepted by the admin
        $demandes=$em->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);
        
        // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
        $validedemandes=$em->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);

        //this code allow us to get all inquiry not responded with the status of null
        $getallinquery=$em->getRepository(Contact::class)->findBy(['status'=>null]);

        // this code is to get all message unread for the user
        $getunread=$em->getRepository(Message::class)->findBy([
            'usertwo'=>$this->getUser(),
            'status'=>"unread",
          ]);

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
            'users'=>$users,
            'validedemandes'=>$validedemandes,
            'demandes'=>$demandes,
            'validedemandes'=>$validedemandes,
            'getarticles'=>$getarticle,
            'unreadmessage'=>$getunread,
            'getallinquery'=>$getallinquery,
        ]);
    }
}