<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251223192455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonnements CHANGE stripe_price_id stripe_price_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE admins CHANGE derniere_connexion derniere_connexion DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE dossiers_medical CHANGE fichiers fichiers JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE medecin_abonnements CHANGE date_expiration date_expiration DATETIME DEFAULT NULL, CHANGE stripe_subscription_id stripe_subscription_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE medecins CHANGE latitude latitude DOUBLE PRECISION DEFAULT NULL, CHANGE longitude longitude DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE patients CHANGE latitude latitude DOUBLE PRECISION DEFAULT NULL, CHANGE longitude longitude DOUBLE PRECISION DEFAULT NULL, CHANGE mutuelle mutuelle VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE rendezvous CHANGE motif motif VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE secretaires CHANGE motif_contrat motif_contrat VARCHAR(255) DEFAULT NULL, CHANGE activation_token activation_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE specialites CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE telephone telephone VARCHAR(20) DEFAULT NULL, CHANGE photo_profil photo_profil VARCHAR(255) DEFAULT NULL, CHANGE roles roles JSON NOT NULL, CHANGE date_modification date_modification DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonnements CHANGE stripe_price_id stripe_price_id VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE admins CHANGE derniere_connexion derniere_connexion DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE dossiers_medical CHANGE fichiers fichiers LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE medecins CHANGE latitude latitude DOUBLE PRECISION DEFAULT \'NULL\', CHANGE longitude longitude DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE medecin_abonnements CHANGE date_expiration date_expiration DATETIME DEFAULT \'NULL\', CHANGE stripe_subscription_id stripe_subscription_id VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE patients CHANGE latitude latitude DOUBLE PRECISION DEFAULT \'NULL\', CHANGE longitude longitude DOUBLE PRECISION DEFAULT \'NULL\', CHANGE mutuelle mutuelle VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE rendezvous CHANGE motif motif VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE secretaires CHANGE activation_token activation_token VARCHAR(255) DEFAULT \'NULL\', CHANGE motif_contrat motif_contrat VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE specialites CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE users CHANGE telephone telephone VARCHAR(20) DEFAULT \'NULL\', CHANGE photo_profil photo_profil VARCHAR(255) DEFAULT \'NULL\', CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE date_modification date_modification DATETIME DEFAULT \'NULL\'');
    }
}
