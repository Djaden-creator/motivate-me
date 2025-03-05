<?php

namespace App\Form;

use App\Entity\user;
use App\Entity\commentaires;
use App\Entity\ReplyComment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ReplyCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('descriptionreply',TextareaType::class, [
                'attr' => ['autocomplete' => 'new-password',
                'placeholder'=> 'Write a comment...',
            ],
                
                'constraints' => [
                    new NotBlank([
                        'message' => 'ce champ ne dois pas etre vide',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReplyComment::class,
        ]);
    }
}
