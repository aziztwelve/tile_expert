<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119062350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE order_items (
                id BIGINT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                order_id BIGINT NOT NULL,
                article_id BIGINT NOT NULL,
                quantity NUMERIC(12,3) NOT NULL,
                unit_price NUMERIC(12,2) NOT NULL,
                unit_price_eur NUMERIC(12,2) DEFAULT NULL,
                currency CHAR(3) DEFAULT NULL,
                measure CHAR(2) DEFAULT NULL,
                weight NUMERIC(10,3) NOT NULL,
                packaging_count NUMERIC(10,3) DEFAULT NULL,
                pallet_qty NUMERIC(10,3) DEFAULT NULL,
                packaging_qty NUMERIC(10,3) DEFAULT NULL,
                swimming_pool BOOLEAN NOT NULL DEFAULT FALSE
            )
        ");

        $this->addSql("CREATE INDEX IDX_items_order ON order_items (order_id)");
        $this->addSql("CREATE INDEX IDX_items_article ON order_items (article_id)");

        $this->addSql("
            ALTER TABLE order_items
            ADD CONSTRAINT FK_items_order
            FOREIGN KEY (order_id) REFERENCES orders (id)
            ON DELETE CASCADE
        ");

        $this->addSql("
            ALTER TABLE order_items
            ADD CONSTRAINT FK_items_article
            FOREIGN KEY (article_id) REFERENCES articles (id)
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE order_items");
    }
}
