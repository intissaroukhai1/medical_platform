<?php

namespace App\Entity;

use App\Repository\SecretaireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SecretaireRepository::class)]
#[ORM\Table(name: 'secretaires')]
class Secretaire extends User
{
      public const TYPE_CDI = 'CDI';
    public const TYPE_CDD = 'CDD';
    public const TYPE_STAGE = 'STAGE';

    public const TYPES_CONTRAT = [
        self::TYPE_CDI,
        self::TYPE_CDD,
        self::TYPE_STAGE,
    ];
    #[ORM\Column(type: 'string', length: 20)]
private string $typeContrat = self::TYPE_CDI;
#[ORM\Column(length: 255, nullable: true)]
    private ?string $activationToken = null;


    #[ORM\ManyToOne(targetEntity: Medecin::class, inversedBy: 'secretaires')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Medecin $medecin = null;

   #[ORM\Column(type: 'string', length: 255, nullable: true)]
   private ?string $motifContrat = null;
   #[ORM\Column(type: 'boolean')]
private bool $actif = true;

    public function __construct()
    {
        parent::__construct();
        
          $this->setRoles(['ROLE_SECRETAIRE']);
           $this->typeContrat = self::TYPE_CDI;
    }

public function getTypeContrat(): string
{
    return $this->typeContrat;
}

public function setTypeContrat(string $type): self
{
    if (!in_array($type, self::TYPES_CONTRAT, true)) {
        throw new \InvalidArgumentException('Type de contrat invalide');
    }

    $this->typeContrat = $type;
    return $this;
}


    public function getMotifContrat(): ?string { return $this->motifContrat; }
  public function setMotifContrat(?string $motif): self
{
    $this->motifContrat = $motif;
    return $this;
}

   


public function isActif(): bool
{
    return $this->actif;
}
public function setActif(bool $actif): self
{
    $this->actif = $actif;
    return $this;
}
  public function getMedecin(): ?Medecin
    {
        return $this->medecin;
    }

    public function setMedecin(?Medecin $medecin): self
    {
        $this->medecin = $medecin;
        return $this;
    }
    public function getActivationToken(): ?string
{
    return $this->activationToken;
}

public function setActivationToken(?string $activationToken): self
{
    $this->activationToken = $activationToken;

    return $this;
}

}
