<?php

namespace App\Form;

use App\Entity\Evaluation;
use App\Entity\Competences;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class NewEvaluationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('heuresCompetence', IntegerType::class, 
            [  
                'label' => "Heures de compétence travaillé",
                'attr' => 
                [
                    'placeholder' => "Heures où la compétence a été travaillé",
                    'min' => 1,
                    'max' => 3,
                ]
            ])
            ->add('competences', EntityType::class, [
                'class' => Competences::class, 
                'choice_label' => 'nom'
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
