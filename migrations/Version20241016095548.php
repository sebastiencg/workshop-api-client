<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241016095548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client_api ADD of_market_place_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE client_api ADD CONSTRAINT FK_8AA14BABD309581B FOREIGN KEY (of_market_place_id) REFERENCES market_place (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8AA14BABD309581B ON client_api (of_market_place_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE client_api DROP CONSTRAINT FK_8AA14BABD309581B');
        $this->addSql('DROP INDEX IDX_8AA14BABD309581B');
        $this->addSql('ALTER TABLE client_api DROP of_market_place_id');
    }
}
