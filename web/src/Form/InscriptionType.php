<?php

namespace App\Form;

use App\Form\ContactType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contact = new ContactType();
        $builder
            ->add('sexe', ChoiceType::class, [
                'label' => "Quel est votre sexe ?",
                'choices' => [
                    'Homme' => "Homme",
                    'Femme' => "Femme"
                ]
            ])
            ->add('nom', TextType::class, $contact->getConfig("Nom", "Votre nom ici"))
            ->add('prenom', TextType::class,  $contact->getConfig("Prénom", "Votre prénom ici"))
            ->add('user', TextType::class, $contact->getConfig("Nom d'utilisateur", "Votre nom d'utilisateur"))
            ->add('naissance', DateType::class, [
                'label'=> "Votre date de naissance",
                'format' => 'dd-MM-yyyy',
                'years' => range(1900, 2020)
            ])
            ->add('mail', EmailType::class, $contact->getConfig("Email", "Votre Email"))
            ->add('mdp', PasswordType::class, $contact->getConfig("Mot de passe", "Choisissez votre mot de passe"))
            ->add('mdpConf', PasswordType::class, $contact->getConfig("Confirmez votre mot de passe", "Confirmation du mot de passe"))
            ->add('save', SubmitType::class, $contact->getConfig("Inscrivez-vous", ""));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
