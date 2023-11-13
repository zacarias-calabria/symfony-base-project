<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231031055705 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carts ADD status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE carts ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE carts ALTER id TYPE VARCHAR(36)');
        $this->addSql('COMMENT ON COLUMN carts.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE carts DROP status');
        $this->addSql('ALTER TABLE carts DROP created_at');
        $this->addSql('ALTER TABLE carts ALTER id TYPE VARCHAR(36)');
    }
}
