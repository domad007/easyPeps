<?php

namespace App\Form;

use App\Entity\Ecole;
use App\Form\ContactType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class NewAppreciationEcoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contact = new ContactType();
        $builder
            ->add('appreciations', CollectionType::class,
            [
                'entry_type' => NewAppreciationType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'label' => false,
            ])
            ->add('save', SubmitType::class, $contact->getConfig("Créez les appréciations", ""))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ecole::class,
        ]);
    }
}
