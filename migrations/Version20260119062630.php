<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119062630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE order_payment (
                order_id BIGINT PRIMARY KEY,
                pay_type SMALLINT NOT NULL,
                vat_type SMALLINT NOT NULL DEFAULT 0,
                vat_number VARCHAR(100) DEFAULT NULL,
                tax_number VARCHAR(50) DEFAULT NULL,
                full_payment_date DATE DEFAULT NULL,
                bank_transfer_requested BOOLEAN DEFAULT FALSE,
                bank_details TEXT DEFAULT NULL,
                cur_rate NUMERIC(10,4) DEFAULT 1,
                payment_euro BOOLEAN DEFAULT FALSE
            )
        ");

        $this->addSql("
            ALTER TABLE order_payment
            ADD CONSTRAINT FK_payment_order
            FOREIGN KEY (order_id) REFERENCES orders (id)
            ON DELETE CASCADE
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE order_payment");
    }
}
