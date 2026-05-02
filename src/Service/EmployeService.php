<?php

namespace App\Service;

use App\Entity\Contrat;
use App\Entity\Employe;
use App\Enum\StatutConge;
use App\Enum\TypeContrat;
use App\Model\ServiceResult;
use App\Repository\CongeRepository;
use App\Repository\ContratRepository;
use App\Repository\DepartementRepository;
use App\Repository\EmployeRepository;
use Doctrine\ORM\EntityManagerInterface;

class EmployeService implements IEmployeService
{
    public function __construct(
        private EntityManagerInterface $em,
        private EmployeRepository $employeRepo,
        private ContratRepository $contratRepo,
        private CongeRepository $congeRepo,
        private DepartementRepository $deptRepo,
    ) {}

    public function validerAgeEmbauche(Employe $employe): bool
    {
        if ($employe->getDateNaissance() === null) return false;
        $age = $employe->getDateNaissance()->diff(new \DateTime())->y;
        return $age >= 18;
    }

    public function creerEmploye(Employe $employe): ServiceResult
    {
        try {
            if (!$this->validerAgeEmbauche($employe)) {
                return ServiceResult::echec('L\'employé doit avoir au moins 18 ans.');
            }

            $this->em->persist($employe);
            $this->em->flush();

            return ServiceResult::ok($employe);
        } catch (\Exception $e) {
            return ServiceResult::echec('Erreur lors de la création : ' . $e->getMessage());
        }
    }

    public function modifierEmploye(Employe $employe): ServiceResult
    {
        try {
            if (!$this->validerAgeEmbauche($employe)) {
                return ServiceResult::echec('L\'employé doit avoir au moins 18 ans.');
            }

            $this->em->flush();
            return ServiceResult::ok($employe);
        } catch (\Exception $e) {
            return ServiceResult::echec('Erreur lors de la modification : ' . $e->getMessage());
        }
    }

    public function supprimerEmploye(int $id): ServiceResult
    {
        try {
            $employe = $this->employeRepo->find($id);
            if (!$employe) {
                return ServiceResult::echec('Employé introuvable.');
            }

            if ($this->contratRepo->aUnCDIActif($employe)) {
                return ServiceResult::echec('Impossible de supprimer un employé avec un contrat CDI actif.');
            }

            if ($this->congeRepo->hasCongesEnAttente($employe)) {
                return ServiceResult::echec('Impossible de supprimer un employé ayant des congés en attente.');
            }

            $this->em->remove($employe);
            $this->em->flush();

            return ServiceResult::ok();
        } catch (\Exception $e) {
            return ServiceResult::echec('Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    public function ajouterContrat(Employe $employe, Contrat $contrat): ServiceResult
    {
        try {
            $contratExistant = $this->contratRepo->findContratActif($employe);
            if ($contratExistant !== null) {
                return ServiceResult::echec('Cet employé a déjà un contrat actif. Clôturez-le d\'abord.');
            }

            $poste = $employe->getPoste();
            if ($poste !== null) {
                $salaire = $contrat->getSalaireBase();
                if ($salaire < $poste->getSalaireMin() || $salaire > $poste->getSalaireMax()) {
                    return ServiceResult::echec(
                        sprintf(
                            'Le salaire %.2f est hors de la fourchette du poste (%.2f - %.2f).',
                            $salaire,
                            $poste->getSalaireMin(),
                            $poste->getSalaireMax()
                        )
                    );
                }
            }

            $dept = $employe->getDepartement();
            if ($dept !== null) {
                $totalActuel = $this->deptRepo->getTotalSalairesActifs($dept->getId());
                if (($totalActuel + $contrat->getSalaireBase()) > $dept->getBudget()) {
                    return ServiceResult::echec(
                        'Le budget du département sera dépassé avec ce contrat.'
                    );
                }
            }

            $contrat->setEmploye($employe);
            $this->em->persist($contrat);
            $this->em->flush();

            return ServiceResult::ok($contrat);
        } catch (\Exception $e) {
            return ServiceResult::echec('Erreur lors de l\'ajout du contrat : ' . $e->getMessage());
        }
    }

    public function transfererEmploye(int $employeId, int $nouveauDepartementId, Contrat $nouveauContrat): ServiceResult
    {
        $this->em->beginTransaction();

        try {
            $employe = $this->employeRepo->find($employeId);
            if (!$employe) {
                return ServiceResult::echec('Employé introuvable.');
            }

            $nouveauDept = $this->deptRepo->find($nouveauDepartementId);
            if (!$nouveauDept) {
                return ServiceResult::echec('Département cible introuvable.');
            }

            $ancienContrat = $this->contratRepo->findContratActif($employe);
            if ($ancienContrat !== null) {
                $ancienContrat->setDateFin(new \DateTime());
            }

            $totalActuel = $this->deptRepo->getTotalSalairesActifs($nouveauDepartementId);
            if (($totalActuel + $nouveauContrat->getSalaireBase()) > $nouveauDept->getBudget()) {
                $this->em->rollback();
                return ServiceResult::echec('Le budget du département cible sera dépassé.');
            }

            $employe->setDepartement($nouveauDept);
            $nouveauContrat->setEmploye($employe);
            $this->em->persist($nouveauContrat);

            $this->em->flush();
            $this->em->commit();

            return ServiceResult::ok($employe);
        } catch (\Exception $e) {
            $this->em->rollback();
            return ServiceResult::echec('Échec du transfert : ' . $e->getMessage());
        }
    }

    public function verifierAnciennete(Employe $employe): bool
    {
        $contrats = $employe->getContrats();
        if ($contrats->isEmpty()) return false;

        $premierContrat = $contrats->first();
        $debut = $premierContrat->getDateDebut();
        if ($debut === null) return false;

        $mois = $debut->diff(new \DateTime())->m + ($debut->diff(new \DateTime())->y * 12);
        return $mois >= 6;
    }

    public function verifierQuotaConge(Employe $employe, int $joursSupplementaires): bool
    {
        $annee = (int) (new \DateTime())->format('Y');
        $dejaConsomme = $this->congeRepo->totalJoursAnnuels($employe, $annee);
        return ($dejaConsomme + $joursSupplementaires) <= 30;
    }
}
