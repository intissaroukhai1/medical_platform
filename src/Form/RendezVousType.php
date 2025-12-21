<?php

namespace App\Form;

use App\Entity\RendezVous;
use App\Entity\Medecin;
use App\Entity\Specialite;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // ✅ ICI ET SEULEMENT ICI
        $medecins = $options['medecins'];

        $builder
            ->add('specialite', EntityType::class, [
                'class' => Specialite::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisir une spécialité',
                'mapped' => false,
                'required' => true,
            ])

            ->add('medecin', EntityType::class, [
                'class' => Medecin::class,
                'choices' => $medecins, // ✅ PLUS D’ERREUR ICI
                'choice_label' => fn (Medecin $m) =>
                    'Dr. ' . $m->getPrenom() . ' ' . $m->getNom(),
                'placeholder' => 'Choisir un médecin',
                'required' => true,
            ])

            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
            ])

            ->add('mode', ChoiceType::class, [
                'choices' => [
                    'Présentiel' => 'presentiel',
                    'Domicile' => 'domicile',
                    'Vidéo' => 'video',
                ],
            ])

            ->add('motif', TextareaType::class, [
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
            'medecins' => [], // 🔴 OBLIGATOIRE SINON WARNING
        ]);
    }
}
