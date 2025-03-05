<?php

namespace App\Controller\Admin;

use App\Entity\Religion;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ReligionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Religion::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
           yield IdField::new('id')->hideOnForm(),
           yield AssociationField::new('user')->setLabel('Posted by'),
           yield TextField::new('brief'),
           yield TextField::new('name')->setLabel('Religion')->hideOnForm(),
           yield ChoiceField::new('name')->setChoices([
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
           yield ImageField::new('picture')->formatValue(static function($value,Religion $religion){
            return $religion->getImagepath();
           })
           ->setBasePath('public/religionpicture')
           ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
           ->setUploadDir('public/religionpicture'),
           yield DateTimeField::new('date'),
           yield TextEditorField::new('description'),
        ];
    }    
}
