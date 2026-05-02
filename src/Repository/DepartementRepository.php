<?php

namespace App\Repository;

use App\Entity\Departement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DepartementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Departement::class);
    }

    public function findByCode(string $code): ?Departement
    {
        return $this->findOneBy(['code' => $code]);
    }

    public function getTotalSalairesActifs(int $departementId): float
    {
        $result = $this->getEntityManager()
            ->createQuery(
                'SELECT SUM(c.salaireBase) FROM App\Entity\Contrat c
                 JOIN c.employe e
                 WHERE e.departement = :deptId
                 AND c.dateDebut <= :now
                 AND (c.dateFin IS NULL OR c.dateFin >= :now)'
            )
            ->setParameter('deptId', $departementId)
            ->setParameter('now', new \DateTime())
            ->getSingleScalarResult();

        return (float) ($result ?? 0.0);
    }
}
