<?php

namespace App\Entity;

use App\Repository\EmployeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmployeRepository::class)]
#[ORM\Table(name: 'employe')]
#[UniqueEntity(fields: ['matricule'], message: 'Ce matricule est déjà utilisé.')]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà enregistré.')]
class Employe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'matricule', type: 'string', length: 20, unique: true)]
    #[Assert\NotBlank(message: 'Le matricule est obligatoire.')]
    private string $matricule = '';

    #[ORM\Column(name: 'nom', type: 'string', length: 80)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    private string $nom = '';

    #[ORM\Column(name: 'prenom', type: 'string', length: 80)]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire.')]
    private string $prenom = '';

    #[ORM\Column(name: 'email', type: 'string', length: 150, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email(message: 'L\'adresse email n\'est pas valide.')]
    private string $email = '';

    #[ORM\Column(name: 'date_naissance', type: 'date')]
    #[Assert\NotNull(message: 'La date de naissance est obligatoire.')]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(name: 'genre', type: 'string', length: 10)]
    #[Assert\Choice(choices: ['Homme', 'Femme'], message: 'Genre invalide.')]
    private string $genre = 'Homme';

    #[ORM\ManyToOne(targetEntity: Departement::class, inversedBy: 'employes')]
    #[ORM\JoinColumn(name: 'departement_id', referencedColumnName: 'id', nullable: true)]
    private ?Departement $departement = null;

    #[ORM\ManyToOne(targetEntity: Poste::class, inversedBy: 'employes')]
    #[ORM\JoinColumn(name: 'poste_id', referencedColumnName: 'id', nullable: true)]
    private ?Poste $poste = null;

    #[ORM\OneToMany(mappedBy: 'employe', targetEntity: Contrat::class, cascade: ['persist', 'remove'])]
    private Collection $contrats;

    #[ORM\OneToMany(mappedBy: 'employe', targetEntity: Evaluation::class, cascade: ['persist', 'remove'])]
    private Collection $evaluations;

    #[ORM\OneToMany(mappedBy: 'employe', targetEntity: Conge::class, cascade: ['persist', 'remove'])]
    private Collection $conges;

    public function __construct()
    {
        $this->contrats = new ArrayCollection();
        $this->evaluations = new ArrayCollection();
        $this->conges = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getMatricule(): string { return $this->matricule; }
    public function setMatricule(string $matricule): static { $this->matricule = $matricule; return $this; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getPrenom(): string { return $this->prenom; }
    public function setPrenom(string $prenom): static { $this->prenom = $prenom; return $this; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getDateNaissance(): ?\DateTimeInterface { return $this->dateNaissance; }
    public function setDateNaissance(?\DateTimeInterface $dateNaissance): static { $this->dateNaissance = $dateNaissance; return $this; }

    public function getGenre(): string { return $this->genre; }
    public function setGenre(string $genre): static { $this->genre = $genre; return $this; }

    public function getDepartement(): ?Departement { return $this->departement; }
    public function setDepartement(?Departement $departement): static { $this->departement = $departement; return $this; }

    public function getPoste(): ?Poste { return $this->poste; }
    public function setPoste(?Poste $poste): static { $this->poste = $poste; return $this; }

    public function getContrats(): Collection { return $this->contrats; }

    public function addContrat(Contrat $contrat): static
    {
        if (!$this->contrats->contains($contrat)) {
            $this->contrats->add($contrat);
            $contrat->setEmploye($this);
        }
        return $this;
    }

    public function removeContrat(Contrat $contrat): static
    {
        $this->contrats->removeElement($contrat);
        return $this;
    }

    public function getEvaluations(): Collection { return $this->evaluations; }

    public function addEvaluation(Evaluation $evaluation): static
    {
        if (!$this->evaluations->contains($evaluation)) {
            $this->evaluations->add($evaluation);
            $evaluation->setEmploye($this);
        }
        return $this;
    }

    public function getConges(): Collection { return $this->conges; }

    public function addConge(Conge $conge): static
    {
        if (!$this->conges->contains($conge)) {
            $this->conges->add($conge);
            $conge->setEmploye($this);
        }
        return $this;
    }

    public function getNomComplet(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function getAge(): int
    {
        if ($this->dateNaissance === null) return 0;
        return $this->dateNaissance->diff(new \DateTime())->y;
    }

    public function getContratActif(): ?Contrat
    {
        $maintenant = new \DateTime();
        foreach ($this->contrats as $contrat) {
            $debut = $contrat->getDateDebut();
            $fin = $contrat->getDateFin();
            if ($debut <= $maintenant && ($fin === null || $fin >= $maintenant)) {
                return $contrat;
            }
        }
        return null;
    }

    public function __toString(): string { return $this->getNomComplet(); }
}
