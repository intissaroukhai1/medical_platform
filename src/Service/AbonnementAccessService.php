<?php

namespace App\Service;

use App\Entity\Medecin;
use App\Entity\Secretaire;
use Doctrine\Common\Collections\Collection;
class AbonnementAccessService
{
    /**
     * Le médecin a-t-il accès à l’application ?
     */
    public function medecinHasAccess(Medecin $medecin): bool
    {
        return $medecin->getActiveAbonnement() !== null;
    }

    /**
     * Peut-on ajouter une nouvelle secrétaire ?
     */
    public function canAddSecretaire(Medecin $medecin): bool
    {
        $active = $medecin->getActiveAbonnement();

        if (!$active) {
            return false;
        }

        $abonnement = $active->getAbonnement();
        if (!$abonnement) {
            return false;
        }

        $quota = $abonnement->getNbSecretairesAutorises();

        // ⚠️ on compte seulement les secrétaires actives
        $secretairesActives = array_filter(
            $medecin->getSecretaires()->toArray(),
            fn (Secretaire $s) => $s->isActif()
        );

        return count($secretairesActives) < $quota;
    }

    /**
     * Une secrétaire a accès si le médecin a un abonnement actif
     */
public function secretaireHasAccess(Secretaire $secretaire): bool
{
    $medecin = $secretaire->getMedecin();

    if (!$medecin) {
        return false;
    }

    return $this->medecinHasAccess($medecin);
}

}
