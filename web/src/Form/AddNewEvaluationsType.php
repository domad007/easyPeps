<?php

namespace App\Form;

use App\Entity\Evaluation;
use App\Form\NewEvaluationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AddNewEvaluationsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('evaluations', CollectionType::class, 
            [
                'entry_type' => NewEvaluationType::class,
                'entry_options' => 
                [
                    'label' => false,
                    'competences' => $options['competences']
                ],
                'allow_add' => true,
                'label' => false,
            ])
            ->add('dateEvaluation', DateType::class, [
                'label' => "Date d'évaluation",
                'data' => new \DateTime(),
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd-MM-yyyy',
                'help' => "Veuillez respecter le format suivant: jj-mm-aaaa"
            ])
            ->add('periode', ChoiceType::class, [
                'choices' => 
                [
                    $options['periodes']
                ],
                'group_by' => 'semestres.intitule',
                'choice_label' => 'nomPeriode',
                'choice_value' => 'id',
                'required' => false
            ])
            ->add('save', SubmitType::class, [
                "label" => "Créez vos évaluations !"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Evaluation::class,
            'periodes' => array(),
            'competences' => array()
        ]);
    }
}
