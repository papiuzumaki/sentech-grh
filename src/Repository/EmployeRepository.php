<?php

namespace App\Repository;

use App\Entity\Employe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class EmployeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employe::class);
    }

    public function findParMatricule(string $matricule): ?Employe
    {
        return $this->findOneBy(['matricule' => $matricule]);
    }

    public function findTousAvecRelations(): QueryBuilder
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.departement', 'd')
            ->addSelect('d')
            ->leftJoin('e.poste', 'p')
            ->addSelect('p')
            ->leftJoin('e.contrats', 'c')
            ->addSelect('c')
            ->orderBy('e.nom', 'ASC');
    }

    public function findParDepartement(int $departementId): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.departement = :dept')
            ->setParameter('dept', $departementId)
            ->orderBy('e.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
