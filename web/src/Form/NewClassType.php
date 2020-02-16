<?php

namespace App\Form;

use App\Entity\Classe;
use App\Form\ContactType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class NewClassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contact = new ContactType();        
        $builder
            ->add('ecole', TextType::class, $contact->getConfig("Le nom de l'école", ""))
            ->add('titulaire', TextType::class, $contact->getConfig("Le nom du titulaire", ""))
            ->add('nomClasse', TextType::class, $contact->getConfig("Le nom de la classe", ""))
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
