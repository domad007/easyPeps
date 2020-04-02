<?php

namespace App\Form;

use App\Entity\Groups;
use App\Form\GroupType;
use App\Form\ContactType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AddGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contact = new ContactType();
        $classes = array();
        foreach($options['classes'] as $key => $value){
            $classes += [$value->getNomClasse() => $value->getId()];
        }

        $builder
        ->add('classes', CollectionType::class, [
            'entry_type' => ChoiceType::class,
            'label' => false,
            'entry_options' => [
                'label' => false,
                'choices' => [
                    $options['classes']
                ],
                'choice_label' => 'nomClasse',
                'choice_value' => 'id'
            ],
            'allow_add' => true,
        ])
        ->add('calculAutomatique', ChoiceType::class, [
            'label' => "Pondération automatique des périodes ?",
            'choices' => 
            [
                'Oui' => 1,
                'Non' => 0
            ],
            'expanded' => true,
            
        ])
        ->add('degre', ChoiceType::class, [
            'label' => "Choix des compétences",
            'choices' => 
            [
               $options['degre']
            ],
            'choice_label' => 'intitule' ,
            'expanded' => true
        ])
        ->add('save', SubmitType::class, $contact->getConfig("Effectuez le groupement", "", "btn-primary btn-lg"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Groups::class,
            'classes' => array(),
            'degre' => array()
        ]);

        $resolver->setAllowedTypes('classes', 'array');
        $resolver->setAllowedTypes('degre', 'array');
    }
}
