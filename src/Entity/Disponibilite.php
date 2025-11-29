<?php

namespace App\Entity;

use App\Repository\DisponibiliteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DisponibiliteRepository::class)]
#[ORM\Table(name: 'disponibilites')]
class Disponibilite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $jour = null;

    #[ORM\Column(type: 'time')]
    private ?\DateTimeInterface $heureDebut = null;

    #[ORM\Column(type: 'time')]
    private ?\DateTimeInterface $heureFin = null;

    #[ORM\Column(type: 'boolean')]
    private bool $actif = true;

    #[ORM\ManyToOne(targetEntity: Medecin::class, inversedBy: 'disponibilites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Medecin $medecin = null;

    public function getId(): ?int { return $this->id; }

    public function getJour(): ?string { return $this->jour; }
    public function setJour(string $jour): self { $this->jour = $jour; return $this; }

    public function getHeureDebut(): ?\DateTimeInterface { return $this->heureDebut; }
    public function setHeureDebut(\DateTimeInterface $h): self { $this->heureDebut = $h; return $this; }

    public function getHeureFin(): ?\DateTimeInterface { return $this->heureFin; }
    public function setHeureFin(\DateTimeInterface $h): self { $this->heureFin = $h; return $this; }

    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): self { $this->actif = $actif; return $this; }

    public function getMedecin(): ?Medecin { return $this->medecin; }
    public function setMedecin(Medecin $m): self { $this->medecin = $m; return $this; }
}
