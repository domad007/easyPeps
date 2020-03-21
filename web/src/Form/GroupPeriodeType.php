<?php

namespace App\Form;

use App\Entity\Groups;
use App\Entity\Periodes;
use App\Form\ContactType;
use App\Form\PeriodesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class GroupPeriodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contact = new ContactType();
        $builder
        ->add('periodes', CollectionType::class, [
            'entry_type' => PeriodesType::class,
            'entry_options' => 
            [
                'label' => false
            ],
            'allow_add' => true,
            'label' => false,
        ])
        ->add('save', SubmitType::class, $contact->getConfig("Créez les périodes", ""))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Groups::class,
        ]);
    }
}
