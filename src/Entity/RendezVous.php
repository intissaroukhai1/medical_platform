<?php

namespace App\Entity;

use App\Repository\RendezVousRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
#[ORM\Table(name: 'rendezvous')]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = null; // confirmé, annulé, en attente

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motif = null;

    #[ORM\Column(length: 50)]
    private ?string $mode = null; // présentiel / vidéo

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $noteSecretaire = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $noteMedecin = null;

    #[ORM\ManyToOne(targetEntity: Patient::class, inversedBy: 'rendezVous')]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(targetEntity: Medecin::class, inversedBy: 'rendezVous')]
    private ?Medecin $medecin = null;

    #[ORM\OneToOne(mappedBy: 'rendezVous', targetEntity: DossierMedical::class, cascade: ['persist', 'remove'])]
    private ?DossierMedical $dossierMedical = null;

    public function getId(): ?int { return $this->id; }

    public function getDate(): ?\DateTimeInterface { return $this->date; }
    public function setDate(\DateTimeInterface $d): self { $this->date = $d; return $this; }

    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $s): self { $this->statut = $s; return $this; }

    public function getMotif(): ?string { return $this->motif; }
    public function setMotif(?string $motif): self { $this->motif = $motif; return $this; }

    public function getMode(): ?string { return $this->mode; }
    public function setMode(string $mode): self { $this->mode = $mode; return $this; }

    public function getNoteSecretaire(): ?string { return $this->noteSecretaire; }
    public function setNoteSecretaire(?string $note): self { $this->noteSecretaire = $note; return $this; }

    public function getNoteMedecin(): ?string { return $this->noteMedecin; }
    public function setNoteMedecin(?string $note): self { $this->noteMedecin = $note; return $this; }

    public function getPatient(): ?Patient { return $this->patient; }
    public function setPatient(?Patient $patient): self { $this->patient = $patient; return $this; }

    public function getMedecin(): ?Medecin { return $this->medecin; }
    public function setMedecin(?Medecin $m): self { $this->medecin = $m; return $this; }

    public function getDossierMedical(): ?DossierMedical { return $this->dossierMedical; }
}
