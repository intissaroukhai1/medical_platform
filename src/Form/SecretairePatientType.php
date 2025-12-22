<?php

namespace App\Form;

use App\Entity\Patient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SecretairePatientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
            ])

            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])

            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
            ])

            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
            ])

            ->add('genre', ChoiceType::class, [
                'label' => 'Genre',
                'choices' => [
                    'Homme' => 'HOMME',
                    'Femme' => 'FEMME',
                ],
                'placeholder' => 'Choisir le genre',
            ])

            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'required' => true,
            ])

            ->add('mutuelle', TextType::class, [
                'label' => 'Mutuelle',
                'required' => false,
            ])

            ->add('groupeSanguin', ChoiceType::class, [
                'label' => 'Groupe sanguin',
                'choices' => [
                    'A+' => 'A+',
                    'A-' => 'A-',
                    'B+' => 'B+',
                    'B-' => 'B-',
                    'AB+' => 'AB+',
                    'AB-' => 'AB-',
                    'O+' => 'O+',
                    'O-' => 'O-',
                ],
                'placeholder' => 'Choisir le groupe sanguin',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Patient::class,
        ]);
    }
}
