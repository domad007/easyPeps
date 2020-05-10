<?php

namespace App\Form;

use App\Entity\Periodes;
use App\Entity\Semestres;
use App\Form\ContactType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PeriodesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contact = new ContactType();
        $builder
            ->add('nomPeriode', TextType::class, $contact->getConfig("Intitulé de la période", "Intitulé"))
            ->add('dateDebut', DateType::class,[
                'label' => "Date d'évaluation",
                'data' => new \DateTime(),
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd-MM-yyyy',
                'help' => "Veuillez respecter le format suivant: jj-mm-aaaa"
            ])
            ->add('dateFin', DateType::class, [
                'label' => "Date d'évaluation",
                'data' => new \DateTime(),
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd-MM-yyyy',
                'attr'=> 
                [
                    'placeholder' => "jj-mm-aaaa"
                ],
                'help' => "Veuillez respecter le format suivant: jj-mm-aaaa"
            ])
            ->add('semestres', EntityType::class, 
            [
                'class' => Semestres::class,
                'choice_label' => 'intitule',
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
