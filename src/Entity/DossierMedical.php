<?php

namespace App\Entity;

use App\Repository\DossierMedicalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DossierMedicalRepository::class)]
#[ORM\Table(name: 'dossiers_medical')]
class DossierMedical
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $contenu = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $fichiers = [];

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $diagnostic = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $ordonnance = null;

    #[ORM\OneToOne(inversedBy: 'dossierMedical', targetEntity: RendezVous::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?RendezVous $rendezVous = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getDateCreation(): ?\DateTimeInterface { return $this->dateCreation; }

    public function getContenu(): ?string { return $this->contenu; }
    public function setContenu(?string $c): self { $this->contenu = $c; return $this; }

    public function getFichiers(): ?array { return $this->fichiers; }
    public function setFichiers(?array $f): self { $this->fichiers = $f; return $this; }

    public function getDiagnostic(): ?string { return $this->diagnostic; }
    public function setDiagnostic(?string $d): self { $this->diagnostic = $d; return $this; }

    public function getOrdonnance(): ?string { return $this->ordonnance; }
    public function setOrdonnance(?string $ord): self { $this->ordonnance = $ord; return $this; }

    public function getRendezVous(): ?RendezVous { return $this->rendezVous; }
    public function setRendezVous(RendezVous $rv): self { $this->rendezVous = $rv; return $this; }
}
