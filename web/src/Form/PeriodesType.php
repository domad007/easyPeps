<?php

namespace App\Form;

use App\Entity\Periodes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class PeriodesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contact = new ContactType();
        $builder
            ->add('nomPeriode', TextType::class, $contact->getConfig("Intitulé de la période", "Intitulé"))
            ->add('pourcentageCours', IntegerType::class, 
            [
                'label' => "Pourcentage de la période",
                'attr' => 
                [
                    'placeholder' => "Le poids de la période en %",
                    'min' => 0,
                    'max' => 100
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Periodes::class,
        ]);
    }
}
