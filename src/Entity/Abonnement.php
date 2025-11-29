<?php

namespace App\Entity;

use App\Repository\AbonnementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AbonnementRepository::class)]
#[ORM\Table(name: 'abonnements')]
class Abonnement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $type = null;

    #[ORM\Column(type: 'float')]
    private ?float $prix = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateExpiration = null;

    #[ORM\Column(type: 'integer')]
    private ?int $nbSecretaireAutorises = 1;

    #[ORM\Column(type: 'boolean')]
    private bool $actif = true;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $modePaiement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $transactionId = null;

    #[ORM\OneToMany(mappedBy: 'abonnement', targetEntity: Medecin::class)]
    private Collection $medecins;

    public function __construct()
    {
        $this->medecins = new ArrayCollection();
        $this->dateDebut = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getType(): ?string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }

    public function getPrix(): ?float { return $this->prix; }
    public function setPrix(float $prix): self { $this->prix = $prix; return $this; }

    public function getDateDebut(): ?\DateTimeInterface { return $this->dateDebut; }
    public function setDateDebut(\DateTimeInterface $d): self { $this->dateDebut = $d; return $this; }

    public function getDateExpiration(): ?\DateTimeInterface { return $this->dateExpiration; }
    public function setDateExpiration(\DateTimeInterface $d): self { $this->dateExpiration = $d; return $this; }

    public function getNbSecretaireAutorises(): ?int { return $this->nbSecretaireAutorises; }
    public function setNbSecretaireAutorises(int $nb): self { $this->nbSecretaireAutorises = $nb; return $this; }

    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): self { $this->actif = $actif; return $this; }

    public function getModePaiement(): ?string { return $this->modePaiement; }
    public function setModePaiement(?string $mode): self { $this->modePaiement = $mode; return $this; }

    public function getTransactionId(): ?string { return $this->transactionId; }
    public function setTransactionId(?string $id): self { $this->transactionId = $id; return $this; }

    /** @return Collection<int, Medecin> */
    public function getMedecins(): Collection { return $this->medecins; }
}
