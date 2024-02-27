<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240227001523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE group_sessions DROP CONSTRAINT fk_9350d16c7e3c61f9');
        $this->addSql('ALTER TABLE groups_users DROP CONSTRAINT fk_4520c24da76ed395');
        $this->addSql('ALTER TABLE group_images DROP CONSTRAINT fk_339966bab03a8386');
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, notification_settings_id INT DEFAULT NULL, uuid UUID NOT NULL, email VARCHAR(180) NOT NULL, displayname VARCHAR(100) DEFAULT NULL, email_verified BOOLEAN DEFAULT false NOT NULL, setup_done BOOLEAN DEFAULT false NOT NULL, auth_code VARCHAR(6) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, urls JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E98C0FABC9 ON users (notification_settings_id)');
        $this->addSql('COMMENT ON COLUMN users.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E98C0FABC9 FOREIGN KEY (notification_settings_id) REFERENCES notification_settings (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT fk_8d93d6498c0fabc9');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('ALTER TABLE group_images DROP CONSTRAINT FK_339966BAB03A8386');
        $this->addSql('ALTER TABLE group_images ADD CONSTRAINT FK_339966BAB03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE group_sessions DROP CONSTRAINT FK_9350D16C7E3C61F9');
        $this->addSql('ALTER TABLE group_sessions ADD CONSTRAINT FK_9350D16C7E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE groups_users DROP CONSTRAINT FK_4520C24DA76ED395');
        $this->addSql('ALTER TABLE groups_users ADD CONSTRAINT FK_4520C24DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE group_images DROP CONSTRAINT FK_339966BAB03A8386');
        $this->addSql('ALTER TABLE group_sessions DROP CONSTRAINT FK_9350D16C7E3C61F9');
        $this->addSql('ALTER TABLE groups_users DROP CONSTRAINT FK_4520C24DA76ED395');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, notification_settings_id INT DEFAULT NULL, uuid UUID NOT NULL, email VARCHAR(180) NOT NULL, displayname VARCHAR(100) DEFAULT NULL, email_verified BOOLEAN DEFAULT false NOT NULL, setup_done BOOLEAN DEFAULT false NOT NULL, auth_code VARCHAR(6) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, urls JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d6498c0fabc9 ON "user" (notification_settings_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649e7927c74 ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_8d93d6498c0fabc9 FOREIGN KEY (notification_settings_id) REFERENCES notification_settings (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E98C0FABC9');
        $this->addSql('DROP TABLE users');
        $this->addSql('ALTER TABLE group_sessions DROP CONSTRAINT fk_9350d16c7e3c61f9');
        $this->addSql('ALTER TABLE group_sessions ADD CONSTRAINT fk_9350d16c7e3c61f9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE groups_users DROP CONSTRAINT fk_4520c24da76ed395');
        $this->addSql('ALTER TABLE groups_users ADD CONSTRAINT fk_4520c24da76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE group_images DROP CONSTRAINT fk_339966bab03a8386');
        $this->addSql('ALTER TABLE group_images ADD CONSTRAINT fk_339966bab03a8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
