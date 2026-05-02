<?php

namespace App\Entity;

use App\Repository\PosteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PosteRepository::class)]
#[ORM\Table(name: 'poste')]
class Poste
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'intitule', type: 'string', length: 100)]
    #[Assert\NotBlank(message: 'L\'intitulé du poste est requis.')]
    private string $intitule = '';

    #[ORM\Column(name: 'niveau_hierarchique', type: 'integer')]
    #[Assert\NotNull]
    #[Assert\Range(min: 1, max: 5, notInRangeMessage: 'Le niveau doit être entre 1 et 5.')]
    private int $niveauHierarchique = 1;

    #[ORM\Column(name: 'salaire_min', type: 'float')]
    #[Assert\Positive(message: 'Le salaire minimum doit être positif.')]
    private float $salaireMin = 0.0;

    #[ORM\Column(name: 'salaire_max', type: 'float')]
    #[Assert\Positive(message: 'Le salaire maximum doit être positif.')]
    private float $salaireMax = 0.0;

    #[ORM\OneToMany(mappedBy: 'poste', targetEntity: Employe::class)]
    private Collection $employes;

    public function __construct()
    {
        $this->employes = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getIntitule(): string { return $this->intitule; }
    public function setIntitule(string $intitule): static { $this->intitule = $intitule; return $this; }

    public function getNiveauHierarchique(): int { return $this->niveauHierarchique; }
    public function setNiveauHierarchique(int $niveauHierarchique): static { $this->niveauHierarchique = $niveauHierarchique; return $this; }

    public function getSalaireMin(): float { return $this->salaireMin; }
    public function setSalaireMin(float $salaireMin): static { $this->salaireMin = $salaireMin; return $this; }

    public function getSalaireMax(): float { return $this->salaireMax; }
    public function setSalaireMax(float $salaireMax): static { $this->salaireMax = $salaireMax; return $this; }

    public function getEmployes(): Collection { return $this->employes; }

    public function addEmploye(Employe $employe): static
    {
        if (!$this->employes->contains($employe)) {
            $this->employes->add($employe);
            $employe->setPoste($this);
        }
        return $this;
    }

    public function removeEmploye(Employe $employe): static
    {
        $this->employes->removeElement($employe);
        return $this;
    }

    public function __toString(): string { return $this->intitule; }
}
