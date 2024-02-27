<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240227002157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE group_images_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE groups_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE notification_settings_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE refresh_tokens_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE group_images (id INT NOT NULL, group_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, urls JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_339966BAFE54D947 ON group_images (group_id)');
        $this->addSql('CREATE INDEX IDX_339966BAB03A8386 ON group_images (created_by_id)');
        $this->addSql('CREATE TABLE groups (id INT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, group_id UUID NOT NULL, image_name VARCHAR(255) DEFAULT NULL, urls JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F06D39707E3C61F9 ON groups (owner_id)');
        $this->addSql('COMMENT ON COLUMN groups.group_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE groups_users (group_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(group_id, user_id))');
        $this->addSql('CREATE INDEX IDX_4520C24DFE54D947 ON groups_users (group_id)');
        $this->addSql('CREATE INDEX IDX_4520C24DA76ED395 ON groups_users (user_id)');
        $this->addSql('CREATE TABLE notification_settings (id INT NOT NULL, new_image_notifications BOOLEAN NOT NULL, new_member_notifications BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE refresh_tokens (id INT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9BACE7E1C74F2195 ON refresh_tokens (refresh_token)');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, notification_settings_id INT DEFAULT NULL, uuid UUID NOT NULL, email VARCHAR(180) NOT NULL, displayname VARCHAR(100) DEFAULT NULL, email_verified BOOLEAN DEFAULT false NOT NULL, setup_done BOOLEAN DEFAULT false NOT NULL, auth_code VARCHAR(6) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, urls JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E98C0FABC9 ON users (notification_settings_id)');
        $this->addSql('COMMENT ON COLUMN users.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE group_images ADD CONSTRAINT FK_339966BAFE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE group_images ADD CONSTRAINT FK_339966BAB03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE groups ADD CONSTRAINT FK_F06D39707E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE groups_users ADD CONSTRAINT FK_4520C24DFE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE groups_users ADD CONSTRAINT FK_4520C24DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E98C0FABC9 FOREIGN KEY (notification_settings_id) REFERENCES notification_settings (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE group_images_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE groups_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE notification_settings_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE refresh_tokens_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('ALTER TABLE group_images DROP CONSTRAINT FK_339966BAFE54D947');
        $this->addSql('ALTER TABLE group_images DROP CONSTRAINT FK_339966BAB03A8386');
        $this->addSql('ALTER TABLE groups DROP CONSTRAINT FK_F06D39707E3C61F9');
        $this->addSql('ALTER TABLE groups_users DROP CONSTRAINT FK_4520C24DFE54D947');
        $this->addSql('ALTER TABLE groups_users DROP CONSTRAINT FK_4520C24DA76ED395');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E98C0FABC9');
        $this->addSql('DROP TABLE group_images');
        $this->addSql('DROP TABLE groups');
        $this->addSql('DROP TABLE groups_users');
        $this->addSql('DROP TABLE notification_settings');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE users');
    }
}
