<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251129211348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonnements CHANGE mode_paiement mode_paiement VARCHAR(50) DEFAULT NULL, CHANGE transaction_id transaction_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE admins CHANGE derniere_connexion derniere_connexion DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE dossiers_medical CHANGE fichiers fichiers JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE patients CHANGE latitude latitude DOUBLE PRECISION DEFAULT NULL, CHANGE longitude longitude DOUBLE PRECISION DEFAULT NULL, CHANGE mutuelle mutuelle VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE rendezvous CHANGE motif motif VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE specialites CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE telephone telephone VARCHAR(20) DEFAULT NULL, CHANGE photo_profil photo_profil VARCHAR(255) DEFAULT NULL, CHANGE roles roles JSON NOT NULL, CHANGE date_modification date_modification DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonnements CHANGE mode_paiement mode_paiement VARCHAR(50) DEFAULT \'NULL\', CHANGE transaction_id transaction_id VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE admins CHANGE derniere_connexion derniere_connexion DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE dossiers_medical CHANGE fichiers fichiers LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE patients CHANGE latitude latitude DOUBLE PRECISION NOT NULL, CHANGE longitude longitude DOUBLE PRECISION NOT NULL, CHANGE mutuelle mutuelle VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE rendezvous CHANGE motif motif VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE specialites CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE users CHANGE telephone telephone VARCHAR(20) DEFAULT \'NULL\', CHANGE photo_profil photo_profil VARCHAR(255) DEFAULT \'NULL\', CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE date_modification date_modification DATETIME DEFAULT \'NULL\'');
    }
}
