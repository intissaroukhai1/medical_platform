<?php

namespace App\Repository;
use App\Entity\Medecin;
use App\Entity\Patient;  


use App\Entity\Ordonnance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrdonnanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ordonnance::class);
    }
    public function countMonthByMedecin(Medecin $medecin): int
{
    $start = new \DateTime('first day of this month 00:00:00');
    $end   = new \DateTime('last day of this month 23:59:59');

    return (int) $this->createQueryBuilder('o')
        ->select('COUNT(o.id)')
        ->andWhere('o.medecin = :medecin')
        ->andWhere('o.createdAt BETWEEN :start AND :end')
        ->setParameter('medecin', $medecin)
        ->setParameter('start', $start)
        ->setParameter('end', $end)
        ->getQuery()
        ->getSingleScalarResult();
}
public function countForPatient(Patient $patient): int
    {
       return (int) $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->where('o.patient = :patient')
            ->setParameter('patient', $patient)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // 📄 Dernières ordonnances
    public function findRecentForPatient(Patient $patient, int $limit = 5): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.patient = :patient')
            ->orderBy('o.dateCreation', 'DESC')
            ->setParameter('patient', $patient)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    public function findForPatient(Patient $patient): array
{
    return $this->createQueryBuilder('o')
        ->leftJoin('o.medecin', 'm')
        ->addSelect('m')
        ->where('o.patient = :patient')
        ->setParameter('patient', $patient)
        ->orderBy('o.createdAt', 'DESC')
        ->getQuery()
        ->getResult();
}

}

