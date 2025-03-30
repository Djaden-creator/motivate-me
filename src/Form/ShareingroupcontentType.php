<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Groups;
use App\Entity\Shareingroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ShareingroupcontentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description',TextareaType::class,[
                'attr' => ['placeholder'=> 'Ecrivez une description...'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'ce champ ne dois pas etre vide,ni la photo',
                    ]),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                new File([
                    'maxSize' => '10024k',
                    'extensions' => [
                        'jpg',
                        'jpeg',
                        'png',
                        'gif',
                    ],
                    'extensionsMessage' => 'Please upload a valid  image',
                ])
            ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Shareingroup::class,
        ]);
    }
}
