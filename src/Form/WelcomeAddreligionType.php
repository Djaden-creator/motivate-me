<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WelcomeAddreligionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('picture',FileType::class,array('data_class' => null))
            ->add('religion', ChoiceType::class,[
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
            ->add('Ajouter', SubmitType::class, [
                'attr'=> array('class'=>'btn btn-primary'),
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
