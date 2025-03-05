<?php

namespace App\Form;

use App\Entity\user;
use App\Entity\Motivateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MotivateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('leaderName',TextType::class)
            ->add('numeroSiret',TextType::class)
            ->add('titre', ChoiceType::class,[
                'choices'  => [
                    'Choisissez votre titre' => 'unnone',
                    'apotre' => 'apotre',
                    'prophete' => 'prophete',
                    'evangeliste' => 'evangeliste',
                    'docteur' => 'docteur',
                    'pasteur' => 'pasteur',
                    'berger' => 'berger',
                    'eveque' => 'eveque',
                    'bishop' => 'bishop',
                    'archi bishop' => 'archi bishop',
                    'Imām' => 'Imām',
                    'Calife' => 'Calife',
                    'Ālim' => 'Ālim',
                    'Shaykh' => 'Shaykh',
                    'Faqīh' => 'Faqīh',
                    'Qādī' => 'Qādī',
                    'Muftī' => 'Muftī',
                    'Ministre des Awqāf' => 'Ministre des Awqāf',
                    'Khatīb' => 'Khatīb',
                    'Dāʿī' => 'Dāʿī',
                    'Mullah' => 'Mullah',
                    'Marjaʿ al-taqlīd' => 'Marjaʿ al-taqlīd',
                    'Pape' => 'Pape',
                    'charges ecclésiastiques' => 'charges ecclésiastiques',
                    'Presbytres' => 'Presbytres',
                    'Diacres' => 'Diacres',
                    'Cardinal' => 'Cardinal',
                    'prêtres' => 'prêtres',
                    'curés' => 'curés',
                    'vicariat' => 'vicariat',
                    'Nonce apostolique' => 'Nonce apostolique',
                    'Primat de l’église' => 'Primat de l’église',
                    'Vicariat général' => 'Vicariat général',
                    'bodhisattvas novices' => 'bodhisattvas novices',
                    'bodhisattvas de la voie' => 'bodhisattvas de la voie',
                    'bodhisattvas irréversibles' => 'bodhisattvas irréversibles',
                    'bodhisattvas du dharmakaya' => 'bodhisattvas du dharmakaya',
                    'maitre' => 'maitre',
                    'guru' => 'guru',
                    'brahmane' => 'brahmane',
                    'kshatriya' => ' kshatriya',
                    'vaishya' => 'vaishya',
                    'shudra' => 'shudra',
                ],
            ])          
            ->add('etablissement',TextType::class)
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
            ->add('picture', FileType::class, [
                'label' => 'image',
                'mapped' => false,

                // unmapped means that this field is not associated to any entity property

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using attributes
                // in the associated entity, so you can use the PHP constraint classes
               
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
