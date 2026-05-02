<?php

namespace App\Service;

use App\Entity\Contrat;
use App\Entity\Employe;
use App\Model\ServiceResult;

interface IEmployeService
{
    public function creerEmploye(Employe $employe): ServiceResult;
    public function modifierEmploye(Employe $employe): ServiceResult;
    public function supprimerEmploye(int $id): ServiceResult;
    public function ajouterContrat(Employe $employe, Contrat $contrat): ServiceResult;
    public function transfererEmploye(int $employeId, int $nouveauDepartementId, Contrat $nouveauContrat): ServiceResult;
    public function validerAgeEmbauche(Employe $employe): bool;
}
