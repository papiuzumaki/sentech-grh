<?php

namespace App\Entity;

use App\Repository\EvaluationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EvaluationRepository::class)]
#[ORM\Table(name: 'evaluation')]
class Evaluation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'periode', type: 'string', length: 50)]
    #[Assert\NotBlank(message: 'La période est obligatoire (ex: T1-2025).')]
    private string $periode = '';

    #[ORM\Column(name: 'note', type: 'float')]
    #[Assert\NotNull]
    #[Assert\Range(min: 0, max: 20, notInRangeMessage: 'La note doit être comprise entre 0 et 20.')]
    private float $note = 0.0;

    #[ORM\Column(name: 'commentaire', type: 'text', nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(name: 'date_evaluation', type: 'date')]
    #[Assert\NotNull]
    private ?\DateTimeInterface $dateEvaluation = null;

    #[ORM\ManyToOne(targetEntity: Employe::class, inversedBy: 'evaluations')]
    #[ORM\JoinColumn(name: 'employe_id', referencedColumnName: 'id', nullable: false)]
    private ?Employe $employe = null;

    public function getId(): ?int { return $this->id; }

    public function getPeriode(): string { return $this->periode; }
    public function setPeriode(string $periode): static { $this->periode = $periode; return $this; }

    public function getNote(): float { return $this->note; }
    public function setNote(float $note): static { $this->note = $note; return $this; }

    public function getCommentaire(): ?string { return $this->commentaire; }
    public function setCommentaire(?string $commentaire): static { $this->commentaire = $commentaire; return $this; }

    public function getDateEvaluation(): ?\DateTimeInterface { return $this->dateEvaluation; }
    public function setDateEvaluation(?\DateTimeInterface $dateEvaluation): static { $this->dateEvaluation = $dateEvaluation; return $this; }

    public function getEmploye(): ?Employe { return $this->employe; }
    public function setEmploye(?Employe $employe): static { $this->employe = $employe; return $this; }
}
