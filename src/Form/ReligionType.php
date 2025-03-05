<?php

namespace App\Form;

use App\Entity\Religion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ReligionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', ChoiceType::class,[
                'choices'  => [
                    'Choose your religion' => 'unnone',
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
                ],
            ])
            ->add('brief',TextType::class)
            ->add('description',TextareaType::class)
            ->add('picture',FileType::class, [
                'label' => 'image',
                'mapped' => false,

                // unmapped means that this field is not associated to any entity property

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using attributes
                // in the associated entity, so you can use the PHP constraint classes
               
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Religion::class,
        ]);
    }
}
