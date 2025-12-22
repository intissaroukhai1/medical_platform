<?php

namespace App\Form;

use App\Entity\Patient;
use App\Entity\RendezVous;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SecretaireRendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            // 👤 Patient
            ->add('patient', EntityType::class, [
                'class' => Patient::class,
                'choice_label' => fn (Patient $p) => $p->getPrenom() . ' ' . $p->getNom(),
                'placeholder' => 'Choisir un patient',
                'label' => 'Patient',
                'required' => true,
            ])

            // 📅 Date & Heure (HTML5 COMPATIBLE)
            ->add('date', DateTimeType::class, [
                'label' => 'Date du rendez-vous',
                'widget' => 'single_text',
                'input'  => 'datetime',    
                'required' => true,
            ])

            // 🎥 Mode
            ->add('mode', ChoiceType::class, [
                'label' => 'Mode',
                'choices' => [
                    'Présentiel' => RendezVous::MODE_PRESENTIEL,
                    'Vidéo'      => RendezVous::MODE_VIDEO,
                ],
                'expanded' => false,
                'multiple' => false,
                'required' => true,
            ]);

        
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}
