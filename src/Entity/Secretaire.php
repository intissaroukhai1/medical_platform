<?php

namespace App\Entity;

use App\Repository\SecretaireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SecretaireRepository::class)]
#[ORM\Table(name: 'secretaires')]
class Secretaire extends User
{
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $typeContrat = null;

    #[ORM\ManyToOne(targetEntity: Medecin::class, inversedBy: 'secretaires')]
    private ?Medecin $medecin = null;

   #[ORM\Column(type: 'string', length: 255, nullable: true)]
   private ?string $motifContrat = null;

    public function __construct()
    {
        parent::__construct();
        $this->setRoles(['ROLE_SECRETAIRE']);
    }

    public function getTypeContrat(): ?string { return $this->typeContrat; }
    public function setTypeContrat(?string $type): self
{
    $this->typeContrat = $type;
    return $this;
}

    public function getMotifContrat(): ?string { return $this->motifContrat; }
  public function setMotifContrat(?string $motif): self
{
    $this->motifContrat = $motif;
    return $this;
}

    public function getMedecin(): ?Medecin { return $this->medecin; }
    public function setMedecin(?Medecin $medecin): self { $this->medecin = $medecin; return $this; }
}
