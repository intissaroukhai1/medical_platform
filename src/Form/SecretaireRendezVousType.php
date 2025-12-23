<?php

namespace App\Form;

use App\Entity\Patient;
use App\Entity\RendezVous;
use App\Entity\Medecin;
use App\Repository\PatientRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SecretaireRendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Medecin $medecin */
        $medecin = $options['medecin'];

        $builder
            ->add('patient', EntityType::class, [
                'class' => Patient::class,
                'choice_label' => fn (Patient $p) =>
                    $p->getPrenom() . ' ' . $p->getNom(),
                'placeholder' => 'Choisir un patient',
                'label' => 'Patient',
                'required' => true,

                // 🔥 FILTRAGE CORRECT
'query_builder' => function (PatientRepository $repo) use ($medecin) {
    return $repo->createQueryBuilder('p')
        ->leftJoin('p.rendezVous', 'r')
        ->leftJoin('r.medecin', 'rm')
        ->where('p.medecin = :medecin OR rm = :medecin')
        ->setParameter('medecin', $medecin)
        ->groupBy('p.id')
        ->orderBy('p.nom', 'ASC');
},

            
            ])

            ->add('date', DateTimeType::class, [
                'label' => 'Date du rendez-vous',
                'widget' => 'single_text',
                'input'  => 'datetime',
                'required' => true,
            ])

            ->add('mode', ChoiceType::class, [
                'label' => 'Mode',
                'choices' => [
                    'Présentiel' => RendezVous::MODE_PRESENTIEL,
                    'Vidéo'      => RendezVous::MODE_VIDEO,
                ],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
            'medecin' => null, // 👈 option personnalisée
        ]);
    }
}
