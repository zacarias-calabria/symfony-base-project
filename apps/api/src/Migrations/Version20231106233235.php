<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231106233235 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE products_in_carts_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE products_in_carts (id INT NOT NULL, cart_id VARCHAR(36) NOT NULL, product_id VARCHAR(36) NOT NULL, unit_price DOUBLE PRECISION NOT NULL, tax_rate DOUBLE PRECISION NOT NULL, quantity INT NOT NULL, added_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_11F444611AD5CDBF ON products_in_carts (cart_id)');
        $this->addSql('CREATE INDEX IDX_11F444614584665A ON products_in_carts (product_id)');
        $this->addSql('CREATE UNIQUE INDEX unique_constraint_idx ON products_in_carts (cart_id, product_id)');
        $this->addSql('COMMENT ON COLUMN products_in_carts.added_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE products_in_carts ADD CONSTRAINT FK_11F444611AD5CDBF FOREIGN KEY (cart_id) REFERENCES carts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE products_in_carts ADD CONSTRAINT FK_11F444614584665A FOREIGN KEY (product_id) REFERENCES products (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE carts ALTER id TYPE VARCHAR(36)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE products_in_carts_id_seq CASCADE');
        $this->addSql('ALTER TABLE products_in_carts DROP CONSTRAINT FK_11F444611AD5CDBF');
        $this->addSql('ALTER TABLE products_in_carts DROP CONSTRAINT FK_11F444614584665A');
        $this->addSql('DROP TABLE products_in_carts');
        $this->addSql('ALTER TABLE carts ALTER id TYPE VARCHAR(36)');
    }
}
