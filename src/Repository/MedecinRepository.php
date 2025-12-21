<?php

namespace App\Repository;

use App\Entity\Medecin;
use App\Entity\Specialite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MedecinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Medecin::class);
    }

    // ✅ MÉTHODE CORRECTE DANS LA CLASSE
    public function findBySpecialite(Specialite $specialite): array
{
    return $this->createQueryBuilder('m')
        ->innerJoin('m.specialites', 's')
        ->where('s = :specialite')
        ->setParameter('specialite', $specialite)
        ->getQuery()
        ->getResult();
}
}
