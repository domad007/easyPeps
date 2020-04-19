<?php

namespace App\Form;

use App\Entity\Appreciation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class NewAppreciationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('intitule', TextType::class,
            [
                'attr' => 
                [
                    'placeholder' => "Nom de l'appréciation",
                ]
            ])
            ->add('cote', IntegerType::class,
            [
                'attr' => 
                [
                    'placeholder' => "A partir de quelle cote appréciation va être appliqué",
                    'min' => 0,
                    'max' => 10
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Appreciation::class,
        ]);
    }
}
