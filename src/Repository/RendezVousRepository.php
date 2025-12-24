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
    public function findAcceptedByMedecin(Medecin $medecin): array
{
    return $this->createQueryBuilder('r')
        ->andWhere('r.medecin = :medecin')
        ->andWhere('r.statut = :statut')
        ->setParameter('medecin', $medecin)
->setParameter('statut', RendezVous::STATUT_CONFIRME)
        ->orderBy('r.date', 'ASC')
        ->getQuery()
        ->getResult();
}
/**
 * 📅 RDV acceptés du jour (dashboard médecin)
 */
public function findTodayAcceptedByMedecin(Medecin $medecin): array
{
    $start = new \DateTime('today 00:00');
    $end   = new \DateTime('today 23:59');

    return $this->createQueryBuilder('r')
        ->andWhere('r.medecin = :medecin')
        ->andWhere('r.statut = :statut')
        ->andWhere('r.date BETWEEN :start AND :end')
        ->setParameter('medecin', $medecin)
        ->setParameter('statut', RendezVous::STATUT_CONFIRME)
        ->setParameter('start', $start)
        ->setParameter('end', $end)
        ->orderBy('r.date', 'ASC')
        ->getQuery()
        ->getResult();
}
/**
 * 📅 Nombre de RDV confirmés du mois (dashboard médecin)
 */
public function countMonthAcceptedByMedecin(Medecin $medecin): int
{
    $start = new \DateTime('first day of this month 00:00:00');
    $end   = new \DateTime('last day of this month 23:59:59');

    return (int) $this->createQueryBuilder('r')
        ->select('COUNT(r.id)')
        ->andWhere('r.medecin = :medecin')
        ->andWhere('r.statut = :statut')
        ->andWhere('r.date BETWEEN :start AND :end')
        ->setParameter('medecin', $medecin)
        ->setParameter('statut', RendezVous::STATUT_CONFIRME)
        ->setParameter('start', $start)
        ->setParameter('end', $end)
        ->getQuery()
        ->getSingleScalarResult();
}
/**
 * 🗓️ RDV d’un médecin pour un mois donné (calendrier)
 */
public function findByMedecinAndMonth(
    Medecin $medecin,
    int $year,
    int $month
): array {
    $start = new \DateTime(sprintf('%d-%02d-01 00:00:00', $year, $month));
    $end   = (clone $start)->modify('last day of this month 23:59:59');

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
 * 📅 Jours avec RDV confirmés pour un mois donné
 */
public function findRdvDaysForMonth(Medecin $medecin, int $year, int $month): array
{
    $start = new \DateTime("$year-$month-01 00:00:00");
    $end   = (clone $start)->modify('last day of this month 23:59:59');

    return $this->createQueryBuilder('r')
        ->select('r.date')
        ->where('r.medecin = :medecin')
        ->andWhere('r.date BETWEEN :start AND :end')
        ->andWhere('r.statut = :statut')
        ->setParameter('medecin', $medecin)
        ->setParameter('statut', RendezVous::STATUT_CONFIRME)
        ->setParameter('start', $start)
        ->setParameter('end', $end)
        ->getQuery()
        ->getResult();
}
 public function countUpcomingForPatientThisMonth(Patient $patient): int
    {
        $start = new \DateTime('first day of this month 00:00:00');
        $end   = new \DateTime('last day of this month 23:59:59');

        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.patient = :patient')
            ->andWhere('r.date BETWEEN :start AND :end')
            ->andWhere('r.statut IN (:statuts)')
            ->setParameter('patient', $patient)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('statuts', ['accepte', 'confirme'])
            ->getQuery()
            ->getSingleScalarResult();
    }

    // 👨‍⚕️ Nombre de médecins distincts suivis par le patient
    public function countDistinctMedecinsForPatient(Patient $patient): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(DISTINCT m.id)')
            ->join('r.medecin', 'm')
            ->where('r.patient = :patient')
            ->setParameter('patient', $patient)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // 📋 Liste des prochains RDV (utile sous le dashboard)
    public function findUpcomingForPatient(Patient $patient, int $limit = 5): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.patient = :patient')
            ->andWhere('r.date >= :now')
            ->setParameter('patient', $patient)
            ->setParameter('now', new \DateTime())
            ->orderBy('r.date', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}

