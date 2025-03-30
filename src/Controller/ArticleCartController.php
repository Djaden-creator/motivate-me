<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Contact;
use App\Entity\Message;
use App\Entity\Motivateur;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleCartController extends AbstractController
{
    /***
     * here we are adding items in the card
     */
    #[Route('/article/cart/{id}', name: 'app_article_cart')]
    public function index(Article $article,$id,SessionInterface $sessionInterface,EntityManagerInterface $entityManagerInterface): Response
    {

        $articleid=$article->getId();
        $panier=$sessionInterface->get('panier',[]);

         // this code is to get all message unread for the user
         $getunread=$entityManagerInterface->getRepository(Message::class)->findBy([
            'usertwo'=>$this->getUser(),
            'status'=>"unread",
          ]);
          //this code allow us to get all inquiry not responded with the status of null
         $getallinquery=$entityManagerInterface->getRepository(Contact::class)->findBy(['status'=>null]);
     
        // si le panier est vide on ajoute le panier
        if(empty($panier[$articleid])){
            $panier[$articleid]=1;
        }
        //on set le panier
        $sessionInterface->set('panier',$panier);
       return $this->redirectToRoute('app_cart',[
         'unreadmessage'=> $getunread,
         'getallinquery'=> $getallinquery,
       ]);
    }


    /***
     * here we are deleting items in the card
     */
    #[Route('/article/delete/{id}', name: 'app_article_delete')]
    public function deleteitem(Article $article,$id,SessionInterface $sessionInterface): Response
    {

        $id=$article->getId();
        $panier=$sessionInterface->get('panier',[]);
         
        // si le panier est vide on ajoute le panier
        if(!empty($panier[$id])){
            unset($panier[$id]);
        }
        //on set le panier
        $sessionInterface->set('panier',$panier);
       return $this->redirectToRoute('app_cart');
    }


    /***
     * here we are deleting all items in the card
     */
    #[Route('/deleteall', name: 'app_alldelete')]
    public function deleteall(SessionInterface $sessionInterface): Response
    {
      $sessionInterface->remove('panier');
       return $this->redirectToRoute('app_cart');
    }

    /**
     * ici we putting the items in the session cart
     */
    #[Route('/cart', name: 'app_cart')]
    public function getsession(SessionInterface $sessionInterface,ArticleRepository $articleRepository,EntityManagerInterface $entityManagerInterface):response
    {
        $session=$this->getUser();
        $user = $entityManagerInterface->getRepository(User::class)->find($session);

        //this cose is to fetch and count all demande not yet accepted by the admin
        $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);
        
        // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
        $validedemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);
       
        //this code allow us to get all inquiry not responded with the status of null
        $getallinquery=$entityManagerInterface->getRepository(Contact::class)->findBy(['status'=>null]);
        // this code is to get all message unread for the user
        $getunread=$entityManagerInterface->getRepository(Message::class)->findBy([
            'usertwo'=>$this->getUser(),
            'status'=>"unread",
          ]);

        $panier=$sessionInterface->get('panier',[]);       
       //on initialise le variable
       $data=[];
       $total=0;

       foreach( $panier as $id =>$quantity)
       {
        $article=$entityManagerInterface->getRepository(Article::class)->find($id);
        $data[]=[
            'article'=>$article,
            'quantity'=>$quantity
        ];
        $total += $article->getPrice() * $quantity;
       }
       
       
       return $this->render('article_cart/index.html.twig',[
        'data'=>$data,
        'total'=>$total,
        'users'=>$user,
        'demandes'=>$demandes,
        'validedemandes'=>$validedemandes,
        'unreadmessage'=> $getunread,
        'getallinquery'=> $getallinquery,  //this variable is used to check if user has a valid demande to add article or not.
       ]);

    }
}
