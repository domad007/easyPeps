<?php

namespace App\Form;

use App\Entity\Eleve;
use App\Entity\Classe;
use App\Form\ContactType;
use App\Form\AddElevesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class EleveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contact = new ContactType();
        
        $builder
            ->add('nom', TextType::class, $contact->getConfig("Nom de l'élève", "Nom"))
            ->add('prenom', TextType::class, $contact->getConfig("Pénom de l'élève", "Prénom"))
            ->add('dateNaissance', DateType::class,
            [
                'label'=> "Date de naissance",
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd-MM-yyyy',
                'attr'=> 
                [
                    'placeholder' => "jj-mm-aaaa"
                ],
                'help' => "Veuillez respecter le format suivant: jj-mm-aaaa"
                
            ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Eleve::class,
        ]);
    }
}
