<?php

namespace App\Repository;

use App\Entity\Patient;
use App\Entity\Medecin;
use App\Entity\RendezVous;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PatientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Patient::class);
    }
 
public function findPatientsForSecretaireMedecin(Medecin $medecin): array
{
    return $this->createQueryBuilder('p')
        ->leftJoin('p.rendezVous', 'r')
        ->leftJoin('r.medecin', 'rm')
        ->where('p.medecin = :medecin OR rm = :medecin')
        ->setParameter('medecin', $medecin)
        ->groupBy('p.id')
        ->orderBy('p.nom', 'ASC')
        ->getQuery()
        ->getResult();
}
public function countByMedecin(Medecin $medecin): int
{
    return (int) $this->createQueryBuilder('p')
        ->select('COUNT(DISTINCT p.id)')
        ->innerJoin('p.rendezVous', 'r')
        ->andWhere('r.medecin = :medecin')
        ->setParameter('medecin', $medecin)
        ->getQuery()
        ->getSingleScalarResult();
}

}
