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
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
        ->add('mail', EmailType::class, [
            'label' => "Votre email",
            'help' => "Vous devez mentionner votre compte gmail pour bénéficier d'agenda",
            'attr' => 
            [
                'placeholder' => "Votre Email ..."
            ]
        ])
        ->add('dateNaiss', DateType::class, [
            'label'=> "Votre date de naissance",
            'widget' => 'single_text',
            'html5' => false,
            'format' => 'dd-MM-yyyy',
            'help' => "Veuillez respecter le format suivant: jj-mm-aaaa"
        ])
        ->add('sexe', ChoiceType::class, [
            'label' => "Quel est votre sexe ?",
            'choices' => [
                'Homme' => "H",
                'Femme' => "F"
            ]
        ])
        ->add('mdp', PasswordType::class, $contact->getConfig("Mot de passe", "Choisissez votre mot de passe"))
        ->add('confMdp', PasswordType::class, $contact->getConfig("Confirmez votre mot de passe", "Confirmez votre mot de passe"))
        ->add('acceptCGU', CheckboxType::class, [
            'label' => "Lu et accepté les",
            'help' => '<a href="CGU" target="_blank">CGU</a>',
            'help_html' => true
        ])
        ->add('recaptcha', EWZRecaptchaType::class,
        [
            'label' => false,
            'language' => 'fr'
        ])
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
