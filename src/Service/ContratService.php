<?php

namespace App\Service;

use App\Entity\Contrat;
use App\Entity\Employe;
use App\Model\ServiceResult;
use App\Repository\ContratRepository;
use Doctrine\ORM\EntityManagerInterface;

class ContratService implements IContratService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ContratRepository $contratRepo,
    ) {}

    public function ajouterContrat(Employe $employe, Contrat $contrat): ServiceResult
    {
        $actif = $this->contratRepo->findContratActif($employe);
        if ($actif !== null) {
            return ServiceResult::echec('Un contrat actif existe déjà pour cet employé.');
        }

        $contrat->setEmploye($employe);
        $this->em->persist($contrat);
        $this->em->flush();

        return ServiceResult::ok($contrat);
    }

    public function cloturerContrat(Contrat $contrat): ServiceResult
    {
        try {
            $contrat->setDateFin(new \DateTime());
            $this->em->flush();
            return ServiceResult::ok();
        } catch (\Exception $e) {
            return ServiceResult::echec('Impossible de clôturer le contrat : ' . $e->getMessage());
        }
    }

    public function verifierSalaire(Contrat $contrat, Employe $employe): bool
    {
        $poste = $employe->getPoste();
        if ($poste === null) return true;

        $s = $contrat->getSalaireBase();
        return $s >= $poste->getSalaireMin() && $s <= $poste->getSalaireMax();
    }
}
