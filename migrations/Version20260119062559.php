<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119062559 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE order_delivery (
                id BIGINT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                order_id BIGINT NOT NULL,
                type SMALLINT NOT NULL DEFAULT 0,
                price NUMERIC(12,2) DEFAULT NULL,
                price_eur NUMERIC(12,2) DEFAULT NULL,
                calculate_type SMALLINT NOT NULL DEFAULT 0,
                date_min DATE DEFAULT NULL,
                date_max DATE DEFAULT NULL,
                confirmed_min DATE DEFAULT NULL,
                confirmed_max DATE DEFAULT NULL,
                fast_pay_min DATE DEFAULT NULL,
                fast_pay_max DATE DEFAULT NULL,
                old_min DATE DEFAULT NULL,
                old_max DATE DEFAULT NULL,
                warehouse_data JSON DEFAULT NULL,
                tracking_number VARCHAR(50) DEFAULT NULL,
                fact_date TIMESTAMP DEFAULT NULL
            )
        ");

        $this->addSql("
            ALTER TABLE order_delivery
            ADD CONSTRAINT FK_delivery_order
            FOREIGN KEY (order_id) REFERENCES orders (id)
            ON DELETE CASCADE
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE order_delivery");
    }
}
