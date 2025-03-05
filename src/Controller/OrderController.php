<?php
namespace App\Controller;

use Stripe\Stripe;
use Stripe\Invoice;
use App\Entity\User;
use App\Bundle\SellMan;
use App\Entity\Article;
use App\Entity\Commande;
use Stripe\StripeClient;
use App\Entity\Addtocart;
use App\Entity\Motivateur;
use App\Entity\Orderdetails;
use Stripe\Checkout\Session;
use Stripe\Exception\CardException;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
  private $gateawy;
  private $manager;
  private $UrlGenerator;

  public function __construct(EntityManagerInterface $manager,UrlGeneratorInterface $urlGenerator)
  {
    $this->manager=$manager;
    $this->gateawy= new StripeClient($_ENV['STRIPE_SECRET_KEY']);
    $this->UrlGenerator=$urlGenerator;
  }
    /**
     * here we are putting all info in the two different table in the database
     */
    #[Route('/order', name: 'app_order')]
    public function index(SessionInterface $sessionInterface,EntityManagerInterface $entityManagerInterface): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user=$this->getUser();
        $panier=$sessionInterface->get('panier',[]);
        if ($panier===[])
        {
         $this->addFlash('success','votre panier est vide');
         return  $this->redirectToRoute('app_blog');
        }
        $order= new Addtocart();
        $order->setUserid($user)
              ->setDate(new \DateTime())
              ->setReference(bin2hex(random_bytes(12)));
        foreach ($panier as $id => $quantity)
         {
           $orderdetail= new Orderdetails();
           $article=$entityManagerInterface->getRepository(Article::class)->find($id);
           $price=$article->getPrice();

           //on cree pour flush

           $orderdetail->setArticleid($article)
                       ->setUser($user)
                       ->setPrice($price)
                       ->setQuantity($quantity)
                       ->setDate(new \DateTime());
        $order->addOrderdetail($orderdetail);

        }
        $entityManagerInterface->persist($order);
        $entityManagerInterface->flush();
        return $this->redirectToRoute('app_payment');
    }



    /**
     * here we are paying in the differente page
     */
    #[Route('/order/payement', name: 'app_payment')]
    public function payitem(request $request,SessionInterface $sessionInterface,EntityManagerInterface $entityManagerInterface,SellMan $sellMan): Response
    {
        $user=$entityManagerInterface->getRepository(User::class)->find($this->getUser());
        
        //this cose is to fetch and count all demande not yet accepted by the admin
        $demandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['decision'=>'traitement encours...']);
        
        // this code is about if the demande of user is accepted he has to see the add article button etc... to add article
        $validedemandes=$entityManagerInterface->getRepository(Motivateur::class)->findby(['user'=>$this->getUser(),'decision'=>'acceptée']);

        $panier=$sessionInterface->get('panier',[]);
        $public_key=$_ENV['STRIPE_PUBLIC_KEY'];
       
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
        if($request->isMethod('POST')){
          $token=$request->get('stripeToken');
          

          \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
          $error=false;
          
          try {
            // here we are creating a customer for stripe if does not exis
          if(!$user->getStripeidtoken()){
            // creation of the user or customer  in stripe
            $sellMan->CreateCustomer($user,$token,$entityManagerInterface);
 
           }else{
             //update the user if existing one
            $sellMan->UpdateCustomer($user,$token);
           }
          
           // here we are charging the user or customment on the credit cart
           //then we changing it to invoic   
            foreach ($panier as $id => $quantity) {
             $getarticle=$entityManagerInterface->getRepository(Article::class)->find($id);
               $sellMan->CreateInvoice(
                   $getarticle->getPrice() * 100,
                   $user->getStripeidtoken(),
                   $getarticle->getCategory(),
                   $_ENV['STRIPE_CURRENCY']
               );
            }
           
           //here we are paying the invoice imediatelly
           $sellMan->PayInvoice($user,true);
          
          } catch (CardException $e) {
            $error="sorry your card was declined due to :".$e->getMessage();
           }
          if(!$error){
            foreach ($panier as $id => $quantity)
            {
              $commande=new Commande(); 
              $article=$entityManagerInterface->getRepository(Article::class)->find($id);
              $commande->setUser($this->getUser())
                       ->setStripeid($user->getStripeidtoken())
                       ->setDateofbuy(new \DateTime())
                       ->setArticle($article);  
                       $entityManagerInterface->persist($commande);
                       $entityManagerInterface->flush(); 
           }                     
                     $sessionInterface->remove('panier');
                     return $this->redirectToRoute('app_success');
                     
          }
          return $this->render('order/failed.html.twig',[
            'error'=>$error,
          ]);
        }
        return $this->render('order/index.html.twig',[
         'data'=>$data,
         'total'=>$total,
         'public_key'=>$public_key,
         'currency'=>$_ENV['STRIPE_CURRENCY'],
         'users'=>$user,
         'demandes'=>$demandes,
         'validedemandes'=>$validedemandes,
         
        ]); 
    }

    /**
     * here we create the succeed page
     */
    #[Route('/success', name: 'app_success')]
    public function succeedpage(Request $request): Response
    {
      return $this->render('order/succed.html.twig');
    }

     /**
     * here we create the error  page
     */
    #[Route('/cancel', name: 'app_cancel')]
    public function cancel(Request $request): response
    {
      return $this->render('order/failed.html.twig');
    }
}
