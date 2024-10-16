<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241015181342 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE continent (id SERIAL NOT NULL, of_user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6CC70C7C5A1B2224 ON continent (of_user_id)');
        $this->addSql('CREATE TABLE fruit (id SERIAL NOT NULL, of_user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A00BD2975A1B2224 ON fruit (of_user_id)');
        $this->addSql('CREATE TABLE fruit_continent (fruit_id INT NOT NULL, continent_id INT NOT NULL, PRIMARY KEY(fruit_id, continent_id))');
        $this->addSql('CREATE INDEX IDX_221C9876BAC115F0 ON fruit_continent (fruit_id)');
        $this->addSql('CREATE INDEX IDX_221C9876921F4C77 ON fruit_continent (continent_id)');
        $this->addSql('CREATE TABLE fruit_type_family (fruit_id INT NOT NULL, type_family_id INT NOT NULL, PRIMARY KEY(fruit_id, type_family_id))');
        $this->addSql('CREATE INDEX IDX_8BBCBB6FBAC115F0 ON fruit_type_family (fruit_id)');
        $this->addSql('CREATE INDEX IDX_8BBCBB6F7798177A ON fruit_type_family (type_family_id)');
        $this->addSql('CREATE TABLE type_family (id SERIAL NOT NULL, of_user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_86314A635A1B2224 ON type_family (of_user_id)');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE continent ADD CONSTRAINT FK_6CC70C7C5A1B2224 FOREIGN KEY (of_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE fruit ADD CONSTRAINT FK_A00BD2975A1B2224 FOREIGN KEY (of_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE fruit_continent ADD CONSTRAINT FK_221C9876BAC115F0 FOREIGN KEY (fruit_id) REFERENCES fruit (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE fruit_continent ADD CONSTRAINT FK_221C9876921F4C77 FOREIGN KEY (continent_id) REFERENCES continent (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE fruit_type_family ADD CONSTRAINT FK_8BBCBB6FBAC115F0 FOREIGN KEY (fruit_id) REFERENCES fruit (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE fruit_type_family ADD CONSTRAINT FK_8BBCBB6F7798177A FOREIGN KEY (type_family_id) REFERENCES type_family (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE type_family ADD CONSTRAINT FK_86314A635A1B2224 FOREIGN KEY (of_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE continent DROP CONSTRAINT FK_6CC70C7C5A1B2224');
        $this->addSql('ALTER TABLE fruit DROP CONSTRAINT FK_A00BD2975A1B2224');
        $this->addSql('ALTER TABLE fruit_continent DROP CONSTRAINT FK_221C9876BAC115F0');
        $this->addSql('ALTER TABLE fruit_continent DROP CONSTRAINT FK_221C9876921F4C77');
        $this->addSql('ALTER TABLE fruit_type_family DROP CONSTRAINT FK_8BBCBB6FBAC115F0');
        $this->addSql('ALTER TABLE fruit_type_family DROP CONSTRAINT FK_8BBCBB6F7798177A');
        $this->addSql('ALTER TABLE type_family DROP CONSTRAINT FK_86314A635A1B2224');
        $this->addSql('DROP TABLE continent');
        $this->addSql('DROP TABLE fruit');
        $this->addSql('DROP TABLE fruit_continent');
        $this->addSql('DROP TABLE fruit_type_family');
        $this->addSql('DROP TABLE type_family');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
