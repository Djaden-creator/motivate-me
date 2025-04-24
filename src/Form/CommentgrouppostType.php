<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Shareingroup;
use App\Entity\Commentgrouppost;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentgrouppostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('comment', TextareaType::class, [
            'attr' => ['autocomplete' => 'new-password',
            'placeholder'=> 'Write a comment...',
        ],
            
            'constraints' => [
                new NotBlank([
                    'message' => 'ce champ ne dois pas etre vide',
                ]),
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentgrouppost::class,
        ]);
    }
}
