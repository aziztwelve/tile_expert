<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119062510 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE order_addresses (
                id BIGINT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                order_id BIGINT NOT NULL,
                type VARCHAR(20) NOT NULL,
                country_id BIGINT DEFAULT NULL,
                region VARCHAR(100) DEFAULT NULL,
                city VARCHAR(200) DEFAULT NULL,
                address VARCHAR(300) DEFAULT NULL,
                building VARCHAR(200) DEFAULT NULL,
                apartment VARCHAR(30) DEFAULT NULL,
                postal_code VARCHAR(20) DEFAULT NULL,
                phone VARCHAR(30) DEFAULT NULL,
                contact_name VARCHAR(255) DEFAULT NULL
            )
        ");

        $this->addSql("CREATE INDEX IDX_addr_order ON order_addresses (order_id)");
        $this->addSql("CREATE INDEX IDX_addr_country ON order_addresses (country_id)");

        $this->addSql("
            ALTER TABLE order_addresses
            ADD CONSTRAINT FK_addr_order
            FOREIGN KEY (order_id) REFERENCES orders (id)
            ON DELETE CASCADE
        ");

        $this->addSql("
            ALTER TABLE order_addresses
            ADD CONSTRAINT FK_addr_country
            FOREIGN KEY (country_id) REFERENCES countries (id)
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE order_addresses");
    }
}
