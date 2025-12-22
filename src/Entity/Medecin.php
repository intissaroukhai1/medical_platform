<?php

namespace App\Entity;

use App\Repository\MedecinRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Secretaire;
#[ORM\Entity(repositoryClass: MedecinRepository::class)]
#[ORM\Table(name: "medecins")]
class Medecin extends User
{
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $numeroOrdre = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $adresseCabinet = null;

    
   #[ORM\Column(type: 'float', nullable: true)]
private ?float $latitude = null;

#[ORM\Column(type: 'float', nullable: true)]
private ?float $longitude = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $ville = null;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $codePostal = null;

    #[ORM\Column(type: 'boolean')]
    private bool $disponibleUrgence = false;

    #[ORM\Column(type: 'float')]
    private ?float $prixConsultation = null;

    #[ORM\Column(type: 'integer')]
    private ?int $experienceAnnees = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $biographie = null;

    // ---------- Relations ----------

    
#[ORM\ManyToMany(targetEntity: Specialite::class, inversedBy: 'medecins')]
#[ORM\JoinTable(name: 'medecin_specialite')]
private Collection $specialites;

    #[ORM\OneToMany(mappedBy: 'medecin', targetEntity: Disponibilite::class, cascade: ['persist', 'remove'])]
    private Collection $disponibilites;

    #[ORM\OneToMany(mappedBy: 'medecin', targetEntity: RendezVous::class)]
    private Collection $rendezVous;
    #[ORM\OneToMany(mappedBy: 'medecin', targetEntity: MedecinAbonnement::class)]
private Collection $abonnements;

#[ORM\OneToMany(mappedBy: 'medecin', targetEntity: Secretaire::class, orphanRemoval: true)]
private Collection $secretaires;

    public function __construct()
    {
        parent::__construct();
        $this->disponibilites = new ArrayCollection();
        $this->rendezVous = new ArrayCollection();
        $this->secretaires = new ArrayCollection();
        $this->specialites = new ArrayCollection();
        $this->abonnements = new ArrayCollection();
        


        // Rôle par défaut d'un médecin
        $this->setRoles(['ROLE_MEDECIN']);
    }

    // ---------- Getters / Setters ----------

    public function getNumeroOrdre(): ?string
    {
        return $this->numeroOrdre;
    }

    public function setNumeroOrdre(string $numeroOrdre): self
    {
        $this->numeroOrdre = $numeroOrdre;
        return $this;
    }

    public function getAdresseCabinet(): ?string
    {
        return $this->adresseCabinet;
    }

    public function setAdresseCabinet(string $adresseCabinet): self
    {
        $this->adresseCabinet = $adresseCabinet;
        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;
        return $this;
    }




    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;
        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): self
    {
        $this->codePostal = $codePostal;
        return $this;
    }

    public function isDisponibleUrgence(): bool
    {
        return $this->disponibleUrgence;
    }

    public function setDisponibleUrgence(bool $disponibleUrgence): self
    {
        $this->disponibleUrgence = $disponibleUrgence;
        return $this;
    }

    public function getPrixConsultation(): ?float
    {
        return $this->prixConsultation;
    }

    public function setPrixConsultation(float $prixConsultation): self
    {
        $this->prixConsultation = $prixConsultation;
        return $this;
    }

    public function getExperienceAnnees(): ?int
    {
        return $this->experienceAnnees;
    }

    public function setExperienceAnnees(int $experienceAnnees): self
    {
        $this->experienceAnnees = $experienceAnnees;
        return $this;
    }

    public function getBiographie(): ?string
    {
        return $this->biographie;
    }

    public function setBiographie(?string $biographie): self
    {
        $this->biographie = $biographie;
        return $this;
    }

    // ---------- Relations ----------

   /**
 * @return Collection<int, Specialite>
 */
public function getSpecialites(): Collection
{
    return $this->specialites;
}

public function addSpecialite(Specialite $specialite): self
{
    if (!$this->specialites->contains($specialite)) {
        $this->specialites->add($specialite);
        $specialite->addMedecin($this); // sync inverse
    }
    return $this;
}

public function removeSpecialite(Specialite $specialite): self
{
    if ($this->specialites->removeElement($specialite)) {
        $specialite->removeMedecin($this); // sync inverse
    }
    return $this;
}

    /**
     * @return Collection<int, Disponibilite>
     */
    public function getDisponibilites(): Collection
    {
        return $this->disponibilites;
    }

    public function addDisponibilite(Disponibilite $disponibilite): self
    {
        if (!$this->disponibilites->contains($disponibilite)) {
            $this->disponibilites[] = $disponibilite;
            $disponibilite->setMedecin($this);
        }
        return $this;
    }

    public function removeDisponibilite(Disponibilite $disponibilite): self
    {
        if ($this->disponibilites->removeElement($disponibilite)) {
            if ($disponibilite->getMedecin() === $this) {
                $disponibilite->setMedecin(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, RendezVous>
     */
    public function getRendezVous(): Collection
    {
        return $this->rendezVous;
    }

    public function addRendezVou(RendezVous $rv): self
    {
        if (!$this->rendezVous->contains($rv)) {
            $this->rendezVous[] = $rv;
            $rv->setMedecin($this);
        }
        return $this;
    }

    public function removeRendezVou(RendezVous $rv): self
    {
        if ($this->rendezVous->removeElement($rv)) {
            if ($rv->getMedecin() === $this) {
                $rv->setMedecin(null);
            }
        }
        return $this;
    }
public function getActiveAbonnement(): ?MedecinAbonnement
{
    foreach ($this->abonnements as $abonnement) {
        if ($abonnement->isActif()) {
            return $abonnement;
        }
    }
    return null;
}
public function getAbonnements(): Collection
{
    return $this->abonnements;
}

public function __toString(): string
{
    return $this->nom ?? 'Médecin';
}
public function getSecretaires(): Collection
{
    return $this->secretaires;
}
public function addSecretaire(Secretaire $secretaire): self
{
    if (!$this->secretaires->contains($secretaire)) {
        $this->secretaires[] = $secretaire;
        $secretaire->setMedecin($this); // synchronisation inverse
    }

    return $this;
}

public function removeSecretaire(Secretaire $secretaire): self
{
    if ($this->secretaires->removeElement($secretaire)) {
        if ($secretaire->getMedecin() === $this) {
            $secretaire->setMedecin(null);
        }
    }

    return $this;
}


}
