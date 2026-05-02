<?php

namespace App\Repository;

use App\Entity\Poste;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PosteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Poste::class);
    }

    public function findParNiveau(int $niveau): array
    {
        return $this->findBy(['niveauHierarchique' => $niveau], ['intitule' => 'ASC']);
    }
}
