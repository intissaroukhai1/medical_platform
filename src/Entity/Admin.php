<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
#[ORM\Table(name: 'admins')]
class Admin extends User
{
    #[ORM\Column(type: 'boolean')]
    private bool $accesTotal = true;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $derniereConnexion = null;

    public function __construct()
    {
        parent::__construct();
        $this->setRoles(['ROLE_ADMIN']);
    }

    public function isAccesTotal(): bool { return $this->accesTotal; }
    public function setAccesTotal(bool $acces): self { $this->accesTotal = $acces; return $this; }

    public function getDerniereConnexion(): ?\DateTimeInterface { return $this->derniereConnexion; }
    public function setDerniereConnexion(?\DateTimeInterface $date): self { $this->derniereConnexion = $date; return $this; }

    // Méthodes UML (optionnelles)
    public function validerMedecin() {}
    public function gérerAbonnements() {}
    public function gérerSpecialites() {}
    public function bannirCompte() {}
}
