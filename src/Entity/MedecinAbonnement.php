<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'medecin_abonnements')]
class MedecinAbonnement
{
    /* ===================== CONSTANTES ===================== */

    public const STATUT_ACTIF   = 'ACTIF';
    public const STATUT_EXPIRE  = 'EXPIRE';
    public const STATUT_ANNULE  = 'ANNULE';

    /* ===================== PROPRIÉTÉS ===================== */

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Medecin::class, inversedBy: 'abonnements')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Medecin $medecin;

    #[ORM\ManyToOne(targetEntity: Abonnement::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Abonnement $abonnement;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $dateDebut;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $dateExpiration = null;

    #[ORM\Column(length: 20)]
    private string $statut = self::STATUT_ACTIF;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripeSubscriptionId = null;

    /* ===================== CONSTRUCTEUR ===================== */

    public function __construct()
    {
        $this->dateDebut = new \DateTimeImmutable();
        $this->statut = self::STATUT_ACTIF;
    }

    /* ===================== GETTERS / SETTERS ===================== */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMedecin(): Medecin
    {
        return $this->medecin;
    }

    public function setMedecin(Medecin $medecin): self
    {
        $this->medecin = $medecin;
        return $this;
    }

    public function getAbonnement(): Abonnement
    {
        return $this->abonnement;
    }

    public function setAbonnement(Abonnement $abonnement): self
    {
        $this->abonnement = $abonnement;
        return $this;
    }

    public function getDateDebut(): \DateTimeImmutable
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeImmutable $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateExpiration(): ?\DateTimeImmutable
    {
        return $this->dateExpiration;
    }

    public function setDateExpiration(?\DateTimeImmutable $dateExpiration): self
    {
        $this->dateExpiration = $dateExpiration;
        return $this;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getStripeSubscriptionId(): ?string
    {
        return $this->stripeSubscriptionId;
    }

    public function setStripeSubscriptionId(?string $stripeSubscriptionId): self
    {
        $this->stripeSubscriptionId = $stripeSubscriptionId;
        return $this;
    }

    /* ===================== LOGIQUE MÉTIER ===================== */

    /**
     * Abonnement réellement actif ?
     */
    public function isActif(): bool
    {
        if ($this->statut !== self::STATUT_ACTIF) {
            return false;
        }

        if ($this->dateExpiration === null) {
            return true;
        }

        return $this->dateExpiration > new \DateTimeImmutable();
    }

    /**
     * Expire proprement l’abonnement
     */
    public function expire(): self
    {
        $this->statut = self::STATUT_EXPIRE;
        $this->dateExpiration = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Annule l’abonnement (admin ou Stripe)
     */
    public function annuler(): self
    {
        $this->statut = self::STATUT_ANNULE;
        $this->dateExpiration = new \DateTimeImmutable();
        return $this;
    }
}
