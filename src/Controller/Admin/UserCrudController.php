<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class UserCrudController extends AbstractCrudController
{
 
  
    public function __construct(private EntityManagerInterface $entityManagerInterface){
       
    }
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
         
        return [
            yield IdField::new('id')->hideonform(),
            yield TextField::new('username'),
            yield TextField::new('religion')->onlyOnIndex(),
            yield ChoiceField::new('religion')->setChoices([
                'athé' => 'athé',
                'Christianisme' => 'Christianisme',
                'Islam' => 'Islam',
                'Buddhism' => 'Buddhism',
                'Confucianism' => 'Confucianism',
                'Taoism' => 'Taoism',
                'Hinduism' => 'Hinduism',
                'Jainism' => 'Jainism',
                'Sikhism' => 'Sikhism',
                'Yoga' => 'Yoga',
                'Judaism' => 'Judaism',
                'Manichaeism' => 'Manichaeism',
                'Yazdânism' => 'Yazdânism',
                'Austroasiatic' => 'Austroasiatic',
                'American' => 'American',
                'Altaic' => 'Altaic',
                'Diasporic African' => 'Diasporic African',
                'African' => 'African',
                'Zoroastrianism' => 'Zoroastrianism',
                'Indo-European' => 'Indo-European',
                'Austronesian' => 'Austronesian',
                'Caucasian' => 'Caucasian',
                'Dravidian' => 'Dravidian',
                'franc-maçonnerie' => 'franc-maçonnerie',
                'Sūkyō Mahikari' => 'Sūkyō Mahikari',
                'Magie noire' => 'Magie noire',
                'Magie blanche' => 'Magie blache',
            ])->onlyOnForms()->setLabel('Religion'),
            yield EmailField::new('email'),
           
            yield ChoiceField::new('gender')->setChoices([
                'Homme' => 'Homme',
                'Femme' => 'Femme',
            ])->onlyOnForms(),
            yield ImageField::new('picture')->formatValue(static function($value,User $user){
                return $user->getImagepath();
            })
            //this code even if u upload a picture with the same name the previous one will not deleted
            ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
            // the admin can not modifiy the user pictures
            ->setUploadDir('public/userpicture'),
           
            yield TextField::new('stripeidtoken')->onlyOnDetail(),
            yield TextField::new('password')->hideOnIndex(),
            yield DateTimeField::new('since'),

            yield ChoiceField::new('roles')
            ->setChoices(array([
                'ROLE_ADMIN'=>'ROLE_ADMIN',
                'ROLE_USER'=>'ROLE_USER',

            ]))
             ->allowMultipleChoices()
             ->renderExpanded()
             ->renderAsBadges(),
           
           
        ];
    }
    
}
