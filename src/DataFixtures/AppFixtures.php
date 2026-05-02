<?php

namespace App\DataFixtures;

use App\Entity\Contrat;
use App\Entity\Departement;
use App\Entity\Employe;
use App\Entity\Poste;
use App\Enum\TypeContrat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $dsi = new Departement();
        $dsi->setNom('Direction des Systèmes d\'Information')
            ->setCode('DSI')
            ->setBudget(15000000);
        $manager->persist($dsi);

        $rh = new Departement();
        $rh->setNom('Ressources Humaines')
            ->setCode('RH')
            ->setBudget(8000000);
        $manager->persist($rh);

        $finance = new Departement();
        $finance->setNom('Finance et Comptabilité')
            ->setCode('FIN')
            ->setBudget(12000000);
        $manager->persist($finance);

        $devSenior = new Poste();
        $devSenior->setIntitule('Développeur Senior')
            ->setNiveauHierarchique(3)
            ->setSalaireMin(500000)
            ->setSalaireMax(900000);
        $manager->persist($devSenior);

        $devJunior = new Poste();
        $devJunior->setIntitule('Développeur Junior')
            ->setNiveauHierarchique(2)
            ->setSalaireMin(250000)
            ->setSalaireMax(450000);
        $manager->persist($devJunior);

        $chefProjet = new Poste();
        $chefProjet->setIntitule('Chef de Projet')
            ->setNiveauHierarchique(4)
            ->setSalaireMin(700000)
            ->setSalaireMax(1200000);
        $manager->persist($chefProjet);

        $comptable = new Poste();
        $comptable->setIntitule('Comptable')
            ->setNiveauHierarchique(2)
            ->setSalaireMin(300000)
            ->setSalaireMax(600000);
        $manager->persist($comptable);

        $rhManager = new Poste();
        $rhManager->setIntitule('Responsable RH')
            ->setNiveauHierarchique(3)
            ->setSalaireMin(450000)
            ->setSalaireMax(750000);
        $manager->persist($rhManager);

        $donneesEmployes = [
            ['EMP001', 'Diallo', 'Mamadou', 'mamadou.diallo@sentech.sn', '1988-03-15', 'Homme',   $dsi,     $devSenior, TypeContrat::CDI,  650000],
            ['EMP002', 'Ndiaye', 'Fatou',   'fatou.ndiaye@sentech.sn',   '1992-07-22', 'Femme',   $rh,      $rhManager, TypeContrat::CDI,  580000],
            ['EMP003', 'Fall',   'Ibrahima','ibrahima.fall@sentech.sn',   '1995-11-08', 'Homme',   $dsi,     $devJunior, TypeContrat::CDD,  320000],
            ['EMP004', 'Sow',    'Aissatou','aissatou.sow@sentech.sn',    '1990-05-30', 'Femme',   $finance, $comptable, TypeContrat::CDI,  420000],
            ['EMP005', 'Ba',     'Oumar',   'oumar.ba@sentech.sn',        '1987-01-12', 'Homme',   $dsi,     $chefProjet,TypeContrat::CDI,  950000],
            ['EMP006', 'Sarr',   'Mariama', 'mariama.sarr@sentech.sn',    '1996-09-03', 'Femme',   $dsi,     $devJunior, TypeContrat::Stage,270000],
            ['EMP007', 'Mbaye',  'Cheikh',  'cheikh.mbaye@sentech.sn',    '1991-04-18', 'Homme',   $finance, $comptable, TypeContrat::CDD,  380000],
            ['EMP008', 'Gueye',  'Rokhaya', 'rokhaya.gueye@sentech.sn',   '1993-12-25', 'Femme',   $rh,      $rhManager, TypeContrat::CDI,  510000],
            ['EMP009', 'Kane',   'Aliou',   'aliou.kane@sentech.sn',      '1989-08-07', 'Homme',   $dsi,     $devSenior, TypeContrat::CDI,  720000],
            ['EMP010', 'Diouf',  'Ndéye',   'ndeye.diouf@sentech.sn',     '1997-02-14', 'Femme',   $finance, $comptable, TypeContrat::Stage,250000],
        ];

        foreach ($donneesEmployes as $data) {
            [$matricule, $nom, $prenom, $email, $dateNaiss, $genre, $dept, $poste, $typeContrat, $salaire] = $data;

            $employe = new Employe();
            $employe->setMatricule($matricule)
                ->setNom($nom)
                ->setPrenom($prenom)
                ->setEmail($email)
                ->setDateNaissance(new \DateTime($dateNaiss))
                ->setGenre($genre)
                ->setDepartement($dept)
                ->setPoste($poste);

            $manager->persist($employe);

            $contrat = new Contrat();
            $contrat->setTypeContrat($typeContrat)
                ->setDateDebut(new \DateTime('-1 year'))
                ->setSalaireBase($salaire)
                ->setPeriodeEssai(false)
                ->setEmploye($employe);

            if ($typeContrat !== TypeContrat::CDI) {
                $contrat->setDateFin(new \DateTime('+6 months'));
            }

            $manager->persist($contrat);
        }

        $manager->flush();
    }
}
