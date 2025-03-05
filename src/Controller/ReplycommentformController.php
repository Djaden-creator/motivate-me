<?php

namespace App\Controller;

use PDO;
use App\Entity\Commentaires;
use App\Entity\ReplyComment;
use App\Form\ReplyCommentType;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\CommentairesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ReplycommentformController extends AbstractController
{


    //here when we click on a button we get a reply form
    #[Route('/getreplyform/{id}', name: 'app_getreplyform')]
    public function getreplyform($id): Response
    {
        return $this->render('replycommentform/form_comment_comment.html.twig',[
            'id'=>$id
        ]);
       
    }

     //here we getting al replies for a particular comment
     #[Route('/getreplies/{id}', name: 'app_getreplies')]
     public function getreplies($id,EntityManagerInterface $entityManagerInterface)
     {

       $getreplies=$entityManagerInterface->getRepository(ReplyComment::class)->findBy(['comment'=>$id],['id'=>'DESC']);

       return $this->render('replycommentform/replycomment.html.twig',[
        'replies'=>$getreplies
    ]);
     }

    // this code is to reply a comment using a form
    #[Route('/replycommentform', name: 'app_replycommentform')]
    public function index(EntityManagerInterface $entityManagerInterface): Response
    {
        // $password =$_ENV['DATABASE_URL'];
        $dsn = 'mysql:host=localhost;dbname=blogtest';
        $username = 'root';
        $password = getenv('');
        
        $pdo = new PDO($dsn,$username,$password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   
        if(isset($_POST['reply'])){

          $idcomment=$_POST['idcomment'];
		  $descriptionreply=$_POST['descriptionreply'];
		  $userid=$_POST['userid'];
          if(empty($descriptionreply)){
           return new Response("<small class='text-danger'>ce champ ne doit pas etre vide</small>");
          }

          $sql="INSERT INTO `reply_comment`(`userid_id`, `comment_id`, `descriptionreply`, `created`) VALUES (:userid,:idcomment,:descriptionreply,NOW())";
          $statement=$pdo->prepare($sql);
          $statement->bindParam(':userid',$userid);
           $statement->bindParam(':idcomment',$idcomment);
           $statement->bindParam(':descriptionreply',$descriptionreply);
          if($statement->execute()){
            $getreplies=$entityManagerInterface->getRepository(ReplyComment::class)->findBy(['comment'=>$idcomment],['id'=>'DESC']);
            return $this->render('replycommentform/replycomment.html.twig',[
                'replies'=>$getreplies
            ]);
          }
          };
    
        return new Response("the network is down comme after");
    }
}
