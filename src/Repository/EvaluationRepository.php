<?php

namespace App\Repository;

use App\Entity\Employe;
use App\Entity\Evaluation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EvaluationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evaluation::class);
    }

    public function findParEmploye(Employe $employe): array
    {
        return $this->findBy(['employe' => $employe], ['dateEvaluation' => 'DESC']);
    }
}
