<?php

namespace App\Repository;

use App\Entity\Conge;
use App\Entity\Employe;
use App\Enum\StatutConge;
use App\Enum\TypeConge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CongeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conge::class);
    }

    public function hasCongesEnAttente(Employe $employe): bool
    {
        $count = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.employe = :emp')
            ->andWhere('c.statut = :statut')
            ->setParameter('emp', $employe)
            ->setParameter('statut', StatutConge::EnAttente)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    // Calcule le total jours de congé annuels pris sur une année donnée
    public function totalJoursAnnuels(Employe $employe, int $annee): int
    {
        $conges = $this->createQueryBuilder('c')
            ->where('c.employe = :emp')
            ->andWhere('c.typeConge = :type')
            ->andWhere('YEAR(c.dateDebut) = :annee')
            ->andWhere('c.statut != :refuse')
            ->setParameter('emp', $employe)
            ->setParameter('type', TypeConge::Annuel)
            ->setParameter('annee', $annee)
            ->setParameter('refuse', StatutConge::Refuse)
            ->getQuery()
            ->getResult();

        $total = 0;
        foreach ($conges as $conge) {
            $total += $conge->getNombreJours();
        }

        return $total;
    }
}
