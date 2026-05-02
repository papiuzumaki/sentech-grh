<?php

namespace App\Entity;

use App\Repository\DepartementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DepartementRepository::class)]
#[ORM\Table(name: 'departement')]
#[UniqueEntity(fields: ['code'], message: 'Ce code de département existe déjà.')]
class Departement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'nom', type: 'string', length: 100)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(min: 3, max: 50)]
    private string $nom = '';

    #[ORM\Column(name: 'code', type: 'string', length: 10, unique: true)]
    #[Assert\NotBlank(message: 'Le code est obligatoire.')]
    #[Assert\Length(min: 3, max: 6)]
    private string $code = '';

    #[ORM\Column(name: 'budget', type: 'float')]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero(message: 'Le budget ne peut pas être négatif.')]
    private float $budget = 0.0;

    #[ORM\OneToMany(mappedBy: 'departement', targetEntity: Employe::class)]
    private Collection $employes;

    public function __construct()
    {
        $this->employes = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getCode(): string { return $this->code; }
    public function setCode(string $code): static { $this->code = $code; return $this; }

    public function getBudget(): float { return $this->budget; }
    public function setBudget(float $budget): static { $this->budget = $budget; return $this; }

    public function getEmployes(): Collection { return $this->employes; }

    public function addEmploye(Employe $employe): static
    {
        if (!$this->employes->contains($employe)) {
            $this->employes->add($employe);
            $employe->setDepartement($this);
        }
        return $this;
    }

    public function removeEmploye(Employe $employe): static
    {
        if ($this->employes->removeElement($employe)) {
            if ($employe->getDepartement() === $this) {
                $employe->setDepartement(null);
            }
        }
        return $this;
    }

    public function __toString(): string { return $this->nom; }
}
