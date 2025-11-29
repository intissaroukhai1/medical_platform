<?php

namespace App\DataFixtures;

use App\Entity\Specialite;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data = [
            [
                'nom' => 'Cardiologie',
                'description' => 'Spécialiste du cœur et du système cardiovasculaire',
                'image' => '/images/cardio.jpg',
            ],
            [
                'nom' => 'Dermatologie',
                'description' => 'Médecin des maladies de la peau',
                'image' => '/images/dermato.jpg',
            ],
            [
                'nom' => 'Orthopédie',
                'description' => 'Spécialiste des os et des articulations',
                'image' => '/images/ortho.jpg',
            ],
            [
                'nom' => 'Psychiatrie',
                'description' => 'Médecin de la santé mentale',
                'image' => '/images/psy.jpg',
            ],
            [
                'nom' => 'Médecine Générale',
                'description' => 'Médecins généralistes pour vos soins du quotidien',
                'image' => '/images/generaliste.jpg',
            ],
        ];

        foreach ($data as $row) {
            $specialite = new Specialite();
            $specialite->setNom($row['nom']);
            $specialite->setDescription($row['description']);
            $specialite->setImage($row['image']);
            $manager->persist($specialite);
        }

        $manager->flush();
    }
}

