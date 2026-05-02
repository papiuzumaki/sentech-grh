<?php

namespace App\Entity;

use App\Enum\TypeContrat;
use App\Repository\ContratRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContratRepository::class)]
#[ORM\Table(name: 'contrat')]
class Contrat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'type_contrat', type: 'string', enumType: TypeContrat::class)]
    #[Assert\NotNull(message: 'Le type de contrat est requis.')]
    private TypeContrat $typeContrat = TypeContrat::CDI;

    #[ORM\Column(name: 'date_debut', type: 'date')]
    #[Assert\NotNull(message: 'La date de début est obligatoire.')]
    private ?\DateTimeInterface $dateDebut = null;

    // nullable pour les CDI
    #[ORM\Column(name: 'date_fin', type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(name: 'salaire_base', type: 'float')]
    #[Assert\Positive(message: 'Le salaire de base doit être supérieur à 0.')]
    private float $salaireBase = 0.0;

    #[ORM\Column(name: 'periode_essai', type: 'boolean', options: ['default' => false])]
    private bool $periodeEssai = false;

    #[ORM\ManyToOne(targetEntity: Employe::class, inversedBy: 'contrats')]
    #[ORM\JoinColumn(name: 'employe_id', referencedColumnName: 'id', nullable: false)]
    private ?Employe $employe = null;

    public function getId(): ?int { return $this->id; }

    public function getTypeContrat(): TypeContrat { return $this->typeContrat; }
    public function setTypeContrat(TypeContrat $typeContrat): static { $this->typeContrat = $typeContrat; return $this; }

    public function getDateDebut(): ?\DateTimeInterface { return $this->dateDebut; }
    public function setDateDebut(?\DateTimeInterface $dateDebut): static { $this->dateDebut = $dateDebut; return $this; }

    public function getDateFin(): ?\DateTimeInterface { return $this->dateFin; }
    public function setDateFin(?\DateTimeInterface $dateFin): static { $this->dateFin = $dateFin; return $this; }

    public function getSalaireBase(): float { return $this->salaireBase; }
    public function setSalaireBase(float $salaireBase): static { $this->salaireBase = $salaireBase; return $this; }

    public function isPeriodeEssai(): bool { return $this->periodeEssai; }
    public function setPeriodeEssai(bool $periodeEssai): static { $this->periodeEssai = $periodeEssai; return $this; }

    public function getEmploye(): ?Employe { return $this->employe; }
    public function setEmploye(?Employe $employe): static { $this->employe = $employe; return $this; }

    public function estActif(): bool
    {
        $now = new \DateTime();
        if ($this->dateDebut > $now) return false;
        if ($this->dateFin !== null && $this->dateFin < $now) return false;
        return true;
    }
}
