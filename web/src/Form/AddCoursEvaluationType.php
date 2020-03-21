<?php

namespace App\Form;

use App\Entity\Cours;
use App\Form\EvaluationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AddCoursEvaluationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contact = new ContactType();
        $builder
            ->add('intitule', TextType::class, $contact->getConfig("Intitulé de l'évaluation", "Intitulé"))
            ->add('nombreHeures', IntegerType::class, 
            [  
                'label' => "Nombre d'heures de cours",
                'attr' => 
                [
                    'placeholder' => "Choisissez le nombre d'heures de cours",
                    'min' => 1,
                    'max' => 3,
                ]
            ])
            ->add('periode', ChoiceType::class, 
            [
                'choices' => 
                [
                    $options['periodes']
                ],
                'choice_label' => 'nomPeriode',
                'choice_value' => 'id',
                'expanded' => true
            ])
            ->add('evaluations', CollectionType::class, [
                'entry_type' => EvaluationType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'label' => false,

            ])
            ->add('save', SubmitType::class, $contact->getConfig("Créez l'évaluation !", ""))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cours::class,
            'periodes' => array()
        ]);

        $resolver->setAllowedTypes('periodes', 'array');
    }
}
