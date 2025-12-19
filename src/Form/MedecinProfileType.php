<?php

namespace App\Form;

use App\Entity\Medecin;
use App\Entity\Specialite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class MedecinProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            // 🔹 Infos professionnelles
            ->add('numeroOrdre', TextType::class, [
                'label' => 'Numéro d’ordre',
            ])
             ->add('latitude', NumberType::class, [
        'label' => 'Latitude',
        'required' => false,
        'scale' => 6,
    ])
    ->add('longitude', NumberType::class, [
        'label' => 'Longitude',
        'required' => false,
        'scale' => 6,
    ])

            ->add('adresseCabinet', TextType::class, [
                'label' => 'Adresse du cabinet',
            ])

            ->add('ville', TextType::class, [
                'label' => 'Ville',
            ])

            ->add('codePostal', TextType::class, [
                'label' => 'Code postal',
            ])

            ->add('prixConsultation', MoneyType::class, [
                'label' => 'Prix consultation',
                'currency' => 'TND',
            ])

            ->add('experienceAnnees', IntegerType::class, [
                'label' => 'Années d’expérience',
            ])

            ->add('biographie', TextareaType::class, [
                'label' => 'Biographie',
                'required' => false,
            ])

            ->add('disponibleUrgence', CheckboxType::class, [
                'label' => 'Disponible pour urgences',
                'required' => false,
            ])

            // 🔹 Spécialités
            ->add('specialites', EntityType::class, [
                'class' => Specialite::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Spécialités',
            ])

            // 🔹 Photo
            ->add('photoProfil', FileType::class, [
                'label' => 'Photo de profil',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '2M',
                        'mimeTypesMessage' => 'Image invalide',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Medecin::class,
        ]);
    }
}
