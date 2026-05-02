<?php

namespace App\Service;

use App\Entity\Contrat;
use App\Entity\Employe;
use App\Model\ServiceResult;

interface IContratService
{
    public function ajouterContrat(Employe $employe, Contrat $contrat): ServiceResult;
    public function cloturerContrat(Contrat $contrat): ServiceResult;
    public function verifierSalaire(Contrat $contrat, Employe $employe): bool;
}
