<?php

namespace App\Form;

use App\Form\ContactType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ModifMdpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contact = new ContactType();
        $builder
            ->add('oldPassword', PasswordType::class, $contact->getConfig("Votre ancien mot de passe", ""))
            ->add('newPassword', PasswordType::class, $contact->getConfig("Votre nouveau mot de passe", ""))
            ->add('confirmPassword', PasswordType::class, $contact->getConfig("Confirmez votre nouveau mot de passe", ""))
            ->add('save', SubmitType::class, $contact->getConfig("Enregistrer votre nouveau mot de passe", ""))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
