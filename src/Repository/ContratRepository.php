<?php

namespace App\Repository;

use App\Entity\Contrat;
use App\Entity\Employe;
use App\Enum\TypeContrat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ContratRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contrat::class);
    }

    public function findContratActif(Employe $employe): ?Contrat
    {
        $now = new \DateTime();
        return $this->createQueryBuilder('c')
            ->where('c.employe = :emp')
            ->andWhere('c.dateDebut <= :now')
            ->andWhere('c.dateFin IS NULL OR c.dateFin >= :now')
            ->setParameter('emp', $employe)
            ->setParameter('now', $now)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function aUnCDIActif(Employe $employe): bool
    {
        $contrat = $this->findContratActif($employe);
        return $contrat !== null && $contrat->getTypeContrat() === TypeContrat::CDI;
    }
}
