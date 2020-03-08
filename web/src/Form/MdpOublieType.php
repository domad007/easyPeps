<?php

namespace App\Form;

use App\Form\ContactType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class MdpOublieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contact = new ContactType();
        $builder
        ->add('newPassword', PasswordType::class, $contact->getConfig("Votre nouveau mot de passe", "Votre mot de passe"))
        ->add('confirmPassword', PasswordType::class,  $contact->getConfig("Confirmez nouveau mot de passe", "Confirmez votre mot de passe"))
        ->add('save', SubmitType::class, $contact->getConfig("Réinitialisez votre mot de passe",""))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
