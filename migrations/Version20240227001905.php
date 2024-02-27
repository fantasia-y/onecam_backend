<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240227001905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE group_sessions_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('ALTER TABLE group_sessions DROP CONSTRAINT fk_9350d16c7e3c61f9');
        $this->addSql('DROP TABLE group_sessions');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE group_sessions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE group_sessions (id INT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, group_id UUID NOT NULL, image_name VARCHAR(255) DEFAULT NULL, urls JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_9350d16c7e3c61f9 ON group_sessions (owner_id)');
        $this->addSql('COMMENT ON COLUMN group_sessions.group_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE group_sessions ADD CONSTRAINT fk_9350d16c7e3c61f9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
