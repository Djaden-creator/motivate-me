<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\LikeReply;
use App\Form\CommentType;
use App\Entity\Motivateur;
use App\Entity\CommentLike;
use App\Entity\Commentaires;
use App\Entity\ReplyComment;
use App\Form\ReplyCommentType;
use Doctrine\ORM\EntityManager;
use App\Repository\ArticleRepository;
use App\Repository\LikeReplyRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentLikeRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\CommentairesRepository;
use App\Repository\ReplyCommentRepository;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\ArgumentResolver\EntityValueResolver;
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
                'validedemandes'=>$validedemandes
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
}
