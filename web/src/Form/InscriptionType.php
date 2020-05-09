<?php

namespace App\Form;

use App\Entity\User;
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
        ->add('nom', TextType::class, $contact->getConfig("Nom", "Votre nom ici"))
        ->add('prenom', TextType::class,  $contact->getConfig("Prénom", "Votre prénom ici"))
        ->add('nomUser', TextType::class, $contact->getConfig("Nom d'utilisateur", "Votre nom d'utilisateur"))
        ->add('mail', EmailType::class, $contact->getConfig("Email", "Votre Email"))
        ->add('dateNaiss', DateType::class, [
            'label'=> "Votre date de naissance",
            'format' => 'dd-MM-yyyy',
            'years' => range(1900, 2020)
        ])
        ->add('sexe', ChoiceType::class, [
            'label' => "Quel est votre sexe ?",
            'choices' => [
                'H' => "Homme",
                'F' => "Femme"
            ]
        ])
        ->add('mdp', PasswordType::class, $contact->getConfig("Mot de passe", "Choisissez votre mot de passe"))
        ->add('confMdp', PasswordType::class, $contact->getConfig("Confirmez votre mot de passe", "Confirmez votre mot de passe"))
        ->add('save', SubmitType::class, $contact->getConfig("Inscrivez-vous", ""));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
