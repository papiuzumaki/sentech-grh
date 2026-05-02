<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260502000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration initiale — Création de toutes les tables du système GRH SENTECH';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TYPE type_contrat AS ENUM ('CDI', 'CDD', 'Stage', 'Freelance')");
        $this->addSql("CREATE TYPE type_conge AS ENUM ('Annuel', 'Maladie', 'Maternite', 'Sans solde')");
        $this->addSql("CREATE TYPE statut_conge AS ENUM ('En attente', 'Approuvé', 'Refusé')");

        $this->addSql('
            CREATE TABLE departement (
                id SERIAL PRIMARY KEY,
                nom VARCHAR(100) NOT NULL,
                code VARCHAR(10) NOT NULL UNIQUE,
                budget DOUBLE PRECISION NOT NULL DEFAULT 0
            )
        ');

        $this->addSql('
            CREATE TABLE poste (
                id SERIAL PRIMARY KEY,
                intitule VARCHAR(100) NOT NULL,
                niveau_hierarchique INT NOT NULL CHECK (niveau_hierarchique BETWEEN 1 AND 5),
                salaire_min DOUBLE PRECISION NOT NULL,
                salaire_max DOUBLE PRECISION NOT NULL,
                CONSTRAINT chk_salaire CHECK (salaire_min < salaire_max)
            )
        ');

        $this->addSql('
            CREATE TABLE employe (
                id SERIAL PRIMARY KEY,
                departement_id INT REFERENCES departement(id) ON DELETE SET NULL,
                poste_id INT REFERENCES poste(id) ON DELETE SET NULL,
                matricule VARCHAR(20) NOT NULL UNIQUE,
                nom VARCHAR(80) NOT NULL,
                prenom VARCHAR(80) NOT NULL,
                email VARCHAR(150) NOT NULL UNIQUE,
                date_naissance DATE NOT NULL,
                genre VARCHAR(10) NOT NULL DEFAULT \'Homme\'
            )
        ');

        $this->addSql('
            CREATE TABLE contrat (
                id SERIAL PRIMARY KEY,
                employe_id INT NOT NULL REFERENCES employe(id) ON DELETE CASCADE,
                type_contrat VARCHAR(20) NOT NULL,
                date_debut DATE NOT NULL,
                date_fin DATE,
                salaire_base DOUBLE PRECISION NOT NULL,
                periode_essai BOOLEAN NOT NULL DEFAULT false
            )
        ');

        $this->addSql('
            CREATE TABLE evaluation (
                id SERIAL PRIMARY KEY,
                employe_id INT NOT NULL REFERENCES employe(id) ON DELETE CASCADE,
                periode VARCHAR(50) NOT NULL,
                note DOUBLE PRECISION NOT NULL CHECK (note >= 0 AND note <= 20),
                commentaire TEXT,
                date_evaluation DATE NOT NULL
            )
        ');

        $this->addSql('
            CREATE TABLE conge (
                id SERIAL PRIMARY KEY,
                employe_id INT NOT NULL REFERENCES employe(id) ON DELETE CASCADE,
                type_conge VARCHAR(20) NOT NULL,
                date_debut DATE NOT NULL,
                date_fin DATE NOT NULL,
                statut VARCHAR(20) NOT NULL DEFAULT \'En attente\',
                motif TEXT,
                CONSTRAINT chk_dates_conge CHECK (date_fin > date_debut)
            )
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS conge');
        $this->addSql('DROP TABLE IF EXISTS evaluation');
        $this->addSql('DROP TABLE IF EXISTS contrat');
        $this->addSql('DROP TABLE IF EXISTS employe');
        $this->addSql('DROP TABLE IF EXISTS poste');
        $this->addSql('DROP TABLE IF EXISTS departement');
        $this->addSql('DROP TYPE IF EXISTS statut_conge');
        $this->addSql('DROP TYPE IF EXISTS type_conge');
        $this->addSql('DROP TYPE IF EXISTS type_contrat');
    }
}
