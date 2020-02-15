<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function getConfig($label, $placeholder, $class=null){
        return [
            'label' => $label,
            'attr' => [
                'class' => $class,
                'placeholder' => $placeholder, 
            ]
        ];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, $this->getConfig("Nom", "Votre nom"))
            ->add('prenom', TextType::class, $this->getConfig("Prénom", "Votre prenom"))
            ->add('mail', EmailType::class, $this->getConfig("Email", "Votre mail de contact"))
            ->add('commentaire', TextareaType::class, $this->getConfig("Commentaire", "Quel message voulez-vous nous faire parvenir ?"))
            ->add('save', SubmitType::class, $this->getConfig("Envoyer", "", "btn btn-primary"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
