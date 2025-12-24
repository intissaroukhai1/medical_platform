<?php

namespace App\Repository;
use Doctrine\ORM\Query\ResultSetMapping;
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
public function searchMedecins(
    ?int $specialiteId,
    float $latitude,
    float $longitude,
    int $radiusKm = 20
): array {
    $rsm = new ResultSetMapping();
    $rsm->addEntityResult(Medecin::class, 'm');
    $rsm->addFieldResult('m', 'id', 'id');
    $rsm->addFieldResult('m', 'latitude', 'latitude');
    $rsm->addFieldResult('m', 'longitude', 'longitude');
    $rsm->addFieldResult('m', 'ville', 'ville');
    $rsm->addFieldResult('m', 'prenom', 'prenom');
    $rsm->addFieldResult('m', 'nom', 'nom');

    $sql = "
        SELECT m.id,
    u.nom,
    u.prenom,
    m.latitude,
    m.longitude,
    m.ville,
        (
            6371 * acos(
                cos(radians(:lat)) * cos(radians(m.latitude)) *
                cos(radians(m.longitude) - radians(:lng)) +
                sin(radians(:lat)) * sin(radians(m.latitude))
            )
        ) AS distance
        FROM medecins m
        INNER JOIN users u ON u.id = m.id
    ";

    if ($specialiteId) {
        $sql .= "
            INNER JOIN medecin_specialite ms ON m.id = ms.medecin_id
            WHERE ms.specialite_id = :specId
            AND m.disponible_urgence = 1
        ";
    } else {
        $sql .= " WHERE m.disponible_urgence = 1 ";
    }

    $sql .= " HAVING distance <= :radius ORDER BY distance ASC";

   $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);

    $query->setParameter('lat', $latitude);
    $query->setParameter('lng', $longitude);
    $query->setParameter('radius', $radiusKm);

    if ($specialiteId) {
        $query->setParameter('specId', $specialiteId);
    }

    return $query->getResult();
}}