<?php

namespace App\Form;

use App\Form\ContactType;
use App\Entity\Ponderation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class NewPonderationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contact = new ContactType();
        $builder
            ->add('evaluation', IntegerType::class,
            [
                'attr' => 
                [
                    'placeholder' => "Evaluation en %",
                    'min' => 1,
                    'max' => 100,
                ]
            ])
            ->add('cours', IntegerType::class,
            [
                'attr' => 
                [
                    'placeholder' => "Cours en %",
                    'min' => 1,
                    'max' => 100,
                ]
            ])
            ->add('save', SubmitType::class, $contact->getConfig("Créez la pondération", ""))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ponderation::class,
        ]);
    }
}
