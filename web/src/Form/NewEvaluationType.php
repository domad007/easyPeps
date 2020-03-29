<?php

namespace App\Form;

use DateTime;
use App\Form\ContactType;
use App\Entity\Evaluation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class NewEvaluationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contact = new ContactType();
        $builder
            ->add('intitule', TextType::class, $contact->getConfig("Intitulé de l'évaluation", "Intitulé de l'évaluation"))
            ->add('heuresCompetence', IntegerType::class,
            [
                'attr' => 
                [
                    'placeholder' => "Choisissez le nombre d'heures de cours",
                    'min' => 1,
                    'max' => 20,
                ]
            ])
            ->add('competence', ChoiceType::class, [
                'choices' => 
                [
                    $options['competences']
                ],
                'choice_label' => 'nom',
                'group_by' => 'typeCompetence.intitule'
            ])
            ->add('surCombien', IntegerType::class, 
            [
                'attr' => 
                [
                    'placeholder' => "Sur combien évaluer",
                    'min' => 1
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Evaluation::class,
            'competences' => array()
        ]);
    }
}
