<?php

namespace App\Entity;

use App\Enum\StatutConge;
use App\Enum\TypeConge;
use App\Repository\CongeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CongeRepository::class)]
#[ORM\Table(name: 'conge')]
class Conge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'type_conge', type: 'string', enumType: TypeConge::class)]
    #[Assert\NotNull(message: 'Le type de congé est requis.')]
    private TypeConge $typeConge = TypeConge::Annuel;

    #[ORM\Column(name: 'date_debut', type: 'date')]
    #[Assert\NotNull(message: 'La date de début est obligatoire.')]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(name: 'date_fin', type: 'date')]
    #[Assert\NotNull(message: 'La date de fin est obligatoire.')]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(name: 'statut', type: 'string', enumType: StatutConge::class)]
    private StatutConge $statut = StatutConge::EnAttente;

    #[ORM\Column(name: 'motif', type: 'text', nullable: true)]
    private ?string $motif = null;

    #[ORM\ManyToOne(targetEntity: Employe::class, inversedBy: 'conges')]
    #[ORM\JoinColumn(name: 'employe_id', referencedColumnName: 'id', nullable: false)]
    private ?Employe $employe = null;

    public function getId(): ?int { return $this->id; }

    public function getTypeConge(): TypeConge { return $this->typeConge; }
    public function setTypeConge(TypeConge $typeConge): static { $this->typeConge = $typeConge; return $this; }

    public function getDateDebut(): ?\DateTimeInterface { return $this->dateDebut; }
    public function setDateDebut(?\DateTimeInterface $dateDebut): static { $this->dateDebut = $dateDebut; return $this; }

    public function getDateFin(): ?\DateTimeInterface { return $this->dateFin; }
    public function setDateFin(?\DateTimeInterface $dateFin): static { $this->dateFin = $dateFin; return $this; }

    public function getStatut(): StatutConge { return $this->statut; }
    public function setStatut(StatutConge $statut): static { $this->statut = $statut; return $this; }

    public function getMotif(): ?string { return $this->motif; }
    public function setMotif(?string $motif): static { $this->motif = $motif; return $this; }

    public function getEmploye(): ?Employe { return $this->employe; }
    public function setEmploye(?Employe $employe): static { $this->employe = $employe; return $this; }

    public function getNombreJours(): int
    {
        if ($this->dateDebut === null || $this->dateFin === null) return 0;
        return (int) $this->dateDebut->diff($this->dateFin)->days;
    }
}
