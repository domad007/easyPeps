<?php

namespace App\Form;

use App\Entity\Ecole;
use App\Entity\Classe;
use App\Form\ContactType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class NewClassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contact = new ContactType();
        $builder
            ->add('nomClasse', TextType::class, $contact->getConfig("Le nom de la classe", ""))
            ->add('titulaire',TextType::class, $contact->getConfig("Le nom du titulaire", ""))
            ->add('ecole', EntityType::class, [
                'class' => 'App:Ecole',
                'choice_label' => 'nomEcole'
            ])
            ->add('save', SubmitType::class, $contact->getConfig("Ajoutez votre classe", ""))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Classe::class,
        ]);
    }
}
