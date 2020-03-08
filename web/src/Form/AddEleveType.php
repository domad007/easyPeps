<?php

namespace App\Form;

use App\Entity\Eleve;
use App\Entity\Classe;
use App\Form\EleveType;
use App\Form\ContactType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AddEleveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contact = new ContactType();
        $builder
            ->add('eleves', CollectionType::class, [
                'entry_type' => EleveType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'label' => false,
            ])
            ->add('save', SubmitType::class, $contact->getConfig("Ajouter à la liste des élèves", ""))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           // 'data_class' => ::class,
        ]);
    }
}
