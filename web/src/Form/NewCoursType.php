<?php

namespace App\Form;

use App\Entity\Cours;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class NewCoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contact = new ContactType();
        $builder
            ->add('intitule', TextType::class, $contact->getConfig("Intitulé du cours", "Intitulé"))
            ->add('nombreHeures', IntegerType::class, 
            [  
                'label' => "Nombre d'heures de cours",
                'attr' => 
                [
                    'placeholder' => "Choisissez le nombre d'heures de cours",
                    'min' => 1,
                    'max' => 3
                ]
            ])
            ->add('periode', ChoiceType::class, 
                [
                    'label' => "Choisissez la période",
                    'choices' => 
                    [
                        'P1' => "P1",
                        'P2' => "P2",
                        'P3' => "P3",
                        'P4' => "P4"
                    ],
                    'expanded' => true,
                ]           
            )
            ->add('save', SubmitType::class, $contact->getConfig("Créez le nouveau cours !", ""))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cours::class,
        ]);
    }
}
