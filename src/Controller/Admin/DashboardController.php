<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Commande;
use App\Entity\Religion;
use App\Entity\Commentaires;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Flex\Path;

#[IsGranted('ROLE_ADMIN')]
#[AdminDashboard(routePath:'/admin' ,routeName:'admin')]
class DashboardController extends AbstractDashboardController
{

    public function __construct(private AdminUrlGenerator $adminUrlGenerator,private EntityManagerInterface $entityManagerInterface)
    {

    }

    public function index(): Response
    {
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        //  $url= $this->adminUrlGenerator->setController(DashboardController::class)->generateUrl();
        //  return $this->redirect($url);
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Motivate me');
    }


    public function configureUserMenu(UserInterface $user): UserMenu//+
    {
        if(!$user instanceof UserInterface){
            throw new \Exception('not you');
        }
       
         $entity = $this->entityManagerInterface;
         $users = $entity->getRepository(User::class)->find($user);
        
           return parent::configureUserMenu($user)
           ->setName($users->getUsername())
          
           ->setAvatarUrl( 'userpicture/'.$users->getPicture())
           ->addMenuItems([
            MenuItem::linkToUrl('My Profile', 'fa fa-id-card', $this->generateUrl('app_profil')),
           //c:\Users\hp\Desktop\blogdev\public\userpicture\admin@gmail.com\bib-67a8e6666e6b5.jpg
        ]);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::subMenu('User', 'fa fa-user')->setSubItems([
              MenuItem::linkToCrud('create User', 'fa fa-tags', User::class)->setAction(Crud::PAGE_NEW),
              MenuItem::linkToCrud('show User', 'fa fa-file-text',User::class),
        ]);
        yield MenuItem::subMenu('Article', 'fa fa-book')->setSubItems([
              MenuItem::linkToCrud('create article', 'fa fa-tags', Article::class)->setAction(Crud::PAGE_NEW),
              MenuItem::linkToCrud('show article', 'fa fa-file-text',Article::class),
        ]);
        yield MenuItem::subMenu('Religion', 'fa fa-id-card-o')->setSubItems([
            MenuItem::linkToCrud('create Religion', 'fa fa-tags', Religion::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('show Religion', 'fa fa-file-text',Religion::class),
        ]);
        yield MenuItem::subMenu('Commande', 'fa fa-shopping-cart')->setSubItems([
            MenuItem::linkToCrud('show commande', 'fa fa-file-text',Commande::class),
        ]);
        yield MenuItem::subMenu('commentaires', 'fa fa-commenting-o')->setSubItems([
            MenuItem::linkToCrud('show Comment', 'fa fa-file-text',Commentaires::class),
        ]);
        // this link is for home page of the site main site
        yield MenuItem::linkToUrl('Homepage', 'fa fa-home',$this->generateUrl('app_blog'));
       
    }
   
    
    // am adding this class to add action show in a single product deatail ex:delete,edit,show
   public function configureActions(): Actions
   {
       return parent::configureActions()
       //this means we are adding an action detail;
      ->add(crud::PAGE_INDEX,Action::DETAIL);
      
   }

}
