<?php

namespace App\Entity;

use App\Repository\PatientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PatientRepository::class)]
#[ORM\Table(name: 'patients')]
class Patient extends User
{
    #[ORM\Column(type: 'string', length: 20)]
    private ?string $numeroCIN = null;
    #[ORM\ManyToOne(targetEntity: Medecin::class, inversedBy: 'patients')]
#[ORM\JoinColumn(nullable: true)]
private ?Medecin $medecin = null;

    #[ORM\Column(type: 'string', length: 10)]
    private ?string $genre = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $adresse = null;

   #[ORM\Column(type: 'float', nullable: true)]
private ?float $latitude = null;

#[ORM\Column(type: 'float', nullable: true)]
private ?float $longitude = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $mutuelle = null;

    #[ORM\Column(type: 'string', length: 10)]
    private ?string $groupeSanguin = null;

    #[ORM\OneToMany(mappedBy: 'patient', targetEntity: RendezVous::class)]
    private Collection $rendezVous;

    public function __construct()
    {
        parent::__construct();
        $this->rendezVous = new ArrayCollection();
        $this->setRoles(['ROLE_PATIENT']);
    }

    public function getNumeroCIN(): ?string { return $this->numeroCIN; }
    public function setNumeroCIN(string $cin): self { $this->numeroCIN = $cin; return $this; }

    public function getGenre(): ?string { return $this->genre; }
    public function setGenre(string $genre): self { $this->genre = $genre; return $this; }

    public function getAdresse(): ?string { return $this->adresse; }
    public function setAdresse(string $adresse): self { $this->adresse = $adresse; return $this; }

    public function getLatitude(): ?float { return $this->latitude; }
    public function setLatitude(float $lat): self { $this->latitude = $lat; return $this; }

    public function getLongitude(): ?float { return $this->longitude; }
    public function setLongitude(float $lon): self { $this->longitude = $lon; return $this; }

    public function getMutuelle(): ?string { return $this->mutuelle; }
    public function setMutuelle(?string $mutuelle): self { $this->mutuelle = $mutuelle; return $this; }

    public function getGroupeSanguin(): ?string { return $this->groupeSanguin; }
    public function setGroupeSanguin(string $grp): self { $this->groupeSanguin = $grp; return $this; }

    /** @return Collection<int, RendezVous> */
    public function getRendezVous(): Collection { return $this->rendezVous; }

   public function addRendezVous(RendezVous $rv): self
{
    if (!$this->rendezVous->contains($rv)) {
        $this->rendezVous[] = $rv;
        $rv->setPatient($this);
    }
    return $this;
}

public function removeRendezVous(RendezVous $rv): self
{
    if ($this->rendezVous->removeElement($rv)) {
        if ($rv->getPatient() === $this) {
            $rv->setPatient(null);
        }
    }
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
}
