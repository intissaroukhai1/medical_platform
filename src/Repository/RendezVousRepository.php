<?php

namespace App\Repository;

use App\Entity\RendezVous;
use App\Entity\Patient;
use App\Entity\Medecin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RendezVousRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RendezVous::class);
    }

    /**
     * 🧑‍⚕️ Tous les RDV d’un patient
     */
    public function findByPatient(Patient $patient): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.patient = :patient')
            ->setParameter('patient', $patient)
            ->orderBy('r.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 👩‍⚕️ Tous les RDV d’un médecin (agenda)
     */
    public function findByMedecin(Medecin $medecin): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.medecin = :medecin')
            ->setParameter('medecin', $medecin)
            ->orderBy('r.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 📅 RDV du jour (secrétaire)
     */
    public function findTodayByMedecin(Medecin $medecin): array
    {
        $start = new \DateTime('today 00:00');
        $end   = new \DateTime('today 23:59');

        return $this->createQueryBuilder('r')
            ->andWhere('r.medecin = :medecin')
            ->andWhere('r.date BETWEEN :start AND :end')
            ->setParameter('medecin', $medecin)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('r.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * ⏳ RDV en attente (workflow secrétaire)
     */
    public function findEnAttenteByMedecin(Medecin $medecin): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.medecin = :medecin')
            ->andWhere('r.statut = :statut')
            ->setParameter('medecin', $medecin)
            ->setParameter('statut', RendezVous::STATUT_EN_ATTENTE)
            ->orderBy('r.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * ❌ RDV annulés
     */
    public function findAnnulesByMedecin(Medecin $medecin): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.medecin = :medecin')
            ->andWhere('r.statut = :statut')
            ->setParameter('medecin', $medecin)
            ->setParameter('statut', RendezVous::STATUT_ANNULE)
            ->orderBy('r.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
