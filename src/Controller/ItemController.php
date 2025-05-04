<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\Contact;
use App\Entity\Message;
use App\Entity\LikeReply;
use App\Form\CommentType;
use App\Entity\Motivateur;
use App\Entity\CommentLike;
use App\Entity\Commentaires;
use App\Entity\ReplyComment;
use App\Repository\ArticleRepository;
use App\Repository\LikeReplyRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentLikeRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\CommentairesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ItemController extends AbstractController
{
    //here we are submiting a new comment to an existing article then we retrieve it dynamically on the page
    #[Route('/item/{id}', name: 'app_item')]
    public function showone(Request $request,$id,ArticleRepository $repos,ManagerRegistry $save,Article $articleid,CommentairesRepository $commentairesRepository,EntityManagerInterface $entityManager): Response
    {
        $user=$this->getUser();
        $users = $entityManager->getRepository(User::class)->find($user);
        $testuser=$users->getReligion();

         // this code is about to fetch all demande of user not yet threated by the admin new demande 
        $demandes=$entityManager->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);

        // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
        $validedemandes=$entityManager->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);
        
        $articleall=$entityManager->getRepository(Article::class);
        $articles=$articleall->findby(['category'=>$testuser]);
        
        $articleid->getId();
        $articlesbyids = $repos->find($id);

        //this code allow us to get all inquiry not responded with the status of null
        $getallinquery=$entityManager->getRepository(Contact::class)->findBy(['status'=>null]);

        // here we are geting if the user got a new unread message in the nav bar
       $getunread=$entityManager->getRepository(Message::class)->findBy([
        'usertwo'=>$this->getUser(),
        'status'=>"unread",
    ]);

        //start here we are updating the nombre de vue of an article
        if($articlesbyids)
        {
            $articleid->setVue($articleid->getVue()+1);
            $entityManager->flush();
        }
        //end of updating the nombre de vue of an article
        $commentaireall=$commentairesRepository->findByExampleField($articleid);
        
        shuffle($articles);
        // here we are adding a comment in a particular comment
        $comment=new Commentaires();
        $formcomment = $this->createForm(CommentType::class,$comment);
        $formcomment->handleRequest($request);
            if ($formcomment->isSubmitted() && $formcomment->isValid()) {
                $em=$save->getManager();
                $comment->setCreateAt(new \DateTimeImmutable())
                        ->setIduser($users)
                        ->setIdarticle($articleid);
                            
                // this condition is needed because the 'brochure' field is not required
                // so the PDF file must be processed only when a file is uploaded
               
                $em->persist($comment);
                $em->flush();
                $comment=$entityManager->getRepository(Commentaires::class)->findBy(['idarticle'=>$articleid],['id'=>'DESC']);
                return $this->render('item/test.html.twig',[
                        'comment'=>$comment
                    ]);
                  
                }
                else {
                    $errors=[];
                    foreach ($formcomment->getErrors(true) as $error) {
                        $errors[]=$error->getMessage();
                        return new JsonResponse(['status'=>'error','errors'=>$errors],400);
                    }
                }
            return $this->render('item/index.html.twig', [
                'articlesbyids'=> $articlesbyids,
                'form'=> $formcomment,
                'commentaireall'=> $commentaireall,
                'articles'=> $articles,
                'users'=>$users,
                'demandes'=>$demandes,
                'validedemandes'=>$validedemandes,
                'unreadmessage'=> $getunread,
                'getallinquery'=>$getallinquery
            ]);
        }

    /**
     *here we are doing the comment like
     *
     */
    #[Route('/item/commentlike/{id}', name: 'app_commentlike')]
    public function CommentLike($id,Commentaires $commentaires,CommentLikeRepository $commentLikeRepository,EntityManagerInterface $manager):response
    {
        $commentaires->getId();
        $userInterface=$this->getUser();
        if(!$userInterface){
            return $this->json(['code'=>415,'message'=>'not connected'],403);
        }
        elseif($commentaires->isLikecommentbyUser($userInterface))
        {
           $commentlike=$commentLikeRepository->findOneBy([
               'commentid'=>$commentaires,
               'userid'=>$userInterface
           ]);
           $manager->remove($commentlike);
           $manager->flush();

           return $this->json([
            'code'=>200,
            'message'=>'like bien supprimé',
            'commentlike'=>$commentLikeRepository->count([
             'commentid'=>$commentaires
            ],200)
        ]);
        }
        else{
            $commentlike= new CommentLike();
            $commentlike->setUserid($userInterface)
                        ->setCommentid($commentaires)
                        ->setCreatedat(new \DateTime());
                 $manager->persist($commentlike);
                 $manager->flush();
            return $this->json(
                ['code'=>200,
                       'message'=>'like ajouté',
                       'commentlike'=>$commentLikeRepository->count([
                       'commentid'=>$commentaires
                ])
                      ],200);
        }
    } 

     /**
     *here we are doing the reply comment like
     *
     */
    #[Route('/item/likereply/{id}', name: 'app_likereply')]
    public function likereply(ReplyComment $replyComment,LikeReplyRepository $likeReplyRepository,EntityManagerInterface $manager):Response
    {
        $userInterface=$this->getUser();
        $replyComment->getId();
        if(!$userInterface){
            return $this->json(['code'=>415,'message'=>'not connected'],403);
        }
        elseif($replyComment->isLikereplycommentbyUser($userInterface))
        {
           $commentlike=$likeReplyRepository->findOneBy([
               'replyid'=>$replyComment,
               'userid'=>$userInterface
           ]);
           $manager->remove($commentlike);
           $manager->flush();

           return $this->json([
            'code'=>200,
            'message'=>'like bien supprimé',
            'replylike'=>$likeReplyRepository->count([
            'replyid'=>$replyComment
            ],200)
        ]);
        } 
        else{
            
                $replylike= new LikeReply();
                $replylike->setUserid($userInterface)
                            ->setReplyid($replyComment)
                            ->setCreatedat(new \DateTime());
                     $manager->persist($replylike);
                     $manager->flush();
                return $this->json(
                    ['code'=>200,
                           'message'=>'like ajouté',
                           'replylike'=>$likeReplyRepository->count([
                           'replyid'=>$replyComment
                           
                    ])
                          ],200);
        }
    }

    #[Route('/formget', name: 'formget')]
    public function getform():Response
    {
       
        return $this->render('item/form_comment_comment.html.twig');
    }

     // here a user trying to search the article
     #[Route('/search_article', name: 'search_article', methods: ['GET'])]
     public function searchgroup(Request $request,EntityManagerInterface $entityManagerInterface) 
     {
       // here we are searching the groups with the specifique religion 
        $query = $request->query->get('query', '');
        
        // we get the user online and his religion to filter article according his religion
        $user=$entityManagerInterface->getRepository(User::class)->find($this->getUser());
        $religion=$user->getReligion();
        //we get the entity fo group
  
        $qb=$entityManagerInterface->createQueryBuilder();
        $qb->select('a')
        ->from(Article::class,'a')
        ->where('a.category = :religion')
        ->andWhere('a.title LIKE :query OR a.topic LIKE :query')
        ->setParameter('query','%'.$query.'%')
        ->setParameter('religion',$religion);
        $results=$qb->getQuery()->getResult();
          
          return $this->render('item/ajax_article.html.twig',[
            'articles'=>$results,
          ]);
     }

    #[Route('/all_religion_article', name: 'all_religion_article')]
    public function allarticlereligion(EntityManagerInterface $entity_manager_interface):Response
    {
        // we are getting the user connected and his religion to filter article
        $users=$entity_manager_interface->getRepository(User::class)->find($this->getUser());
        $religion=$users->getReligion();

        //filter article according user religion
        $allpost=$entity_manager_interface->getRepository(Article::class)->findBy(['category'=>$religion],['id'=>'DESC']);

         // this code is about to fetch all demande of user not yet threated by the admin new demande 
        $demandes=$entity_manager_interface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);

        // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
        $validedemandes=$entity_manager_interface->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);

        //this code allow us to get all inquiry not responded with the status of null
        $getallinquery=$entity_manager_interface->getRepository(Contact::class)->findBy(['status'=>null]);

        // here we are geting if the user got a new unread message in the nav bar
       $getunread=$entity_manager_interface->getRepository(Message::class)->findBy([
        'usertwo'=>$this->getUser(),
        'status'=>"unread",
    ]);
       
        return $this->render('item/filter_article.html.twig',[
            'articles'=>$allpost,
            'users'=>$users,
                'demandes'=>$demandes,
                'validedemandes'=>$validedemandes,
                'unreadmessage'=> $getunread,
                'getallinquery'=>$getallinquery
        ]);
    }

    // here to dwnload a file when it is free
    #[Route('/download_pdf/{id}', name: 'app_fichier_pdf')]
    public function downloadFile($id,ArticleRepository $articleRepository,Article $ar,EntityManagerInterface $entityManagerInterface): Response
    {
    
          // we are getting the user connected and his religion to filter article
          $users=$entityManagerInterface->getRepository(User::class)->find($this->getUser());
          // this code is about to fetch all demande of user not yet threated by the admin new demande 
          $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);

          // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
          $validedemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);
  
          //this code allow us to get all inquiry not responded with the status of null
          $getallinquery=$entityManagerInterface->getRepository(Contact::class)->findBy(['status'=>null]);
  
          // here we are geting if the user got a new unread message in the nav bar
         $getunread=$entityManagerInterface->getRepository(Message::class)->findBy([
          'usertwo'=>$this->getUser(),
          'status'=>"unread",
      ]);

        $file =$articleRepository->find($id); 

        $ar->getId();
       
            //start here we are updating the nombre de download of an article
        if($file)
        {
            $ar->setDownload($ar->getDownload()+1);
            $entityManagerInterface->flush();
        }
       
        try {
          
            $getfichier=$file->getFichier();
            $path = $this->getParameter('fichier').'/'.$getfichier;
            if (!file_exists($path)) {
               throw $this->createNotFoundException("oups the file does not exist");
               
            }

            $response=new BinaryFileResponse($path);
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT,$getfichier);
            return $response;
        } catch (Exception $e) {
            throw $this->createNotFoundException("oups the file does not exist");
        }
        // return $this->render('item/downloaded.html.twig',[
        //     'getfichier'=>$getfichier,
        //     'demandes'=>$demandes,
        //     'validedemandes'=>$validedemandes,
        //     'unreadmessage'=> $getunread,
        //     'getallinquery'=>$getallinquery,
        //     'users'=>$users,
            
           
        // ]);
           
        }   
}