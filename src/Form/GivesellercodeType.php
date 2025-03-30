<?php

namespace App\Form;

use App\Entity\user;
use App\Entity\Motivateur;
use App\Entity\Generatorcode;
use Symfony\Component\Form\AbstractType;
use App\Repository\GeneratorcodeRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class GivesellercodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('decision',ChoiceType::class,[
                'placeholder'=>'choose a decision',
                'choices'=>[
                     'acceptée' => 'acceptée',
                     'rejetée' => 'rejetée',
                ]
             ])
             ->add('motif',ChoiceType::class,[
                'placeholder'=>'choose a reason',
                'choices'=>[
                     'votre dossier est complet et valide' => 'votre dossier est complet et valide',
                     'votre dossier manques de cible' => 'votre dossier manques de cible',
                     'votre dossier manques de numero siret' => 'votre dossier manques de numero siret',
                ]
             ])
            ->add('codeseller', EntityType::class,[
                'class' => Generatorcode::class,
                'query_builder' => function (GeneratorcodeRepository $er) {
                    return $er->createQueryBuilder('g')
                        ->where('g.status = :value')
                        ->setParameter('value', 'disponible')
                        ->orderBy('g.id', 'ASC');
                },
                'choice_label' => 'codenumber',
                'multiple' => false,
 
            ])
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Motivateur::class,
        ]);
    }
}
