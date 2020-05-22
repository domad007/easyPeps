<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ContactType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CompteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contact = new ContactType();
        $builder
            ->add('nom', TextType::class, $contact->getConfig("Modifiez votre nom", ""))
            ->add('prenom', TextType::class, $contact->getConfig("Modifiez votre prenom", ""))
            ->add('nomUser', TextType::class, $contact->getConfig("Modifiez votre nom d'utilisateur", ""))
            ->add('mail', EmailType::class, $contact->getConfig("Modifiez votre Email", ""))
            ->add('userActif', CheckboxType::class, [
                "label" => "Desactiver mon compte",
                'required' => false,
                'attr' => 
                [
                    'onchange' => "desactivate(this)"
                ]
                
            ])
            ->add('recaptcha', EWZRecaptchaType::class,
            [
                'label' => false,
                'language' => 'fr'
            ])
            ->add('save', SubmitType::class, $contact->getConfig("Enregistrez vos modifications", ""))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
