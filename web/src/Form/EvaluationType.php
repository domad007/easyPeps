<?php

namespace App\Form;

use App\Entity\Evaluation;
use App\Entity\Competences;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class EvaluationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('competences', EntityType::class, [
                'class' => Competences::class,
                'choice_label' => 'nom'
            ])
            ->add('heuresCompetence', IntegerType::class, [
                'attr' => 
                [
                    'placeholder' => "Heures travaillés de la compétence",
                    'min' => 1,
                    'max' => 20,
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Evaluation::class,
        ]);
    }
}
