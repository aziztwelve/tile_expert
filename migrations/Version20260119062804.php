<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119062804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE order_carrier (
                id BIGINT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                order_id BIGINT NOT NULL,
                name VARCHAR(100) DEFAULT NULL,
                contact_data VARCHAR(255) DEFAULT NULL
            )
        ");

        $this->addSql("
            ALTER TABLE order_carrier
            ADD CONSTRAINT FK_carrier_order
            FOREIGN KEY (order_id) REFERENCES orders (id)
            ON DELETE CASCADE
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE order_carrier");
    }
}
