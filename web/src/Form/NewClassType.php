<?php

namespace App\Form;

use App\Entity\Classe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
           /* ->add('ecole', CollectionType::class, [
                'entry_type' => TextType::class
            ])*/
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
