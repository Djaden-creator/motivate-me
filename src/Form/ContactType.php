<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('purpose',ChoiceType::class,[
            'placeholder'=>'choose a decision',
            'choices'=>[
                 'Sur ma demande pour devenir un motivateur' => 'Sur ma demande pour devenir un motivateur',
                 'Probleme sur mon count motivateur' => 'Probleme sur mon count motivateur',
                 'Mon count montivateur a été bloqué' => 'Mon count montivateur a été bloqué',
                 'Autres' => 'Autres',
            ]
            ])
            ->add('description',TextareaType::class, [
                'label' => 'Message',
                'required' => true,
            ])
            ->add('suivinumber',TextType::class, [
                'label' => 'Message',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
