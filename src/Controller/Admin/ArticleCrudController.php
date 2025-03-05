<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use Monolog\DateTimeImmutable;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
           yield IdField::new('id')->hideOnForm(),
           yield TextField::new('title'),
           yield ChoiceField::new('category')->setChoices([
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
        yield ChoiceField::new('status')->setChoices([
            'free' => 'free',
            'payable' => 'payable',
        ])->onlyOnForms(),
           yield TextField::new('category')->setLabel('Religion')->hideOnForm(),
           yield TextField::new('status')->hideOnForm(),
           yield AssociationField::new('userposter')->setLabel('Posted by'),
           yield MoneyField::new('price')->setCurrency('EUR'),
           yield TextField::new('vue')->onlyOnDetail(),
           yield TextareaField::new('topic')->hideOnIndex(),
           yield DateTimeField::new('createAt')->hideOnIndex(),
           yield ImageField::new('pictures')->formatValue(static function($value,Article $article){
            return $article->getImagepath();
        })->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
        ->setUploadDir('public/uploadarticle/'),
        ];
    }
    

}
