<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119062036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE orders (
                id BIGINT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                hash CHAR(32) NOT NULL,
                number VARCHAR(20) DEFAULT NULL,
                user_id BIGINT DEFAULT NULL,
                manager_id BIGINT DEFAULT NULL,
                status SMALLINT NOT NULL DEFAULT 1,
                locale CHAR(5) NOT NULL,
                currency CHAR(3) NOT NULL DEFAULT 'EUR',
                measure CHAR(3) NOT NULL DEFAULT 'm',
                discount_percent SMALLINT DEFAULT NULL,
                name VARCHAR(200) NOT NULL,
                description TEXT DEFAULT NULL,
                created_at TIMESTAMP NOT NULL,
                updated_at TIMESTAMP DEFAULT NULL
            )
        ");

        $this->addSql("CREATE UNIQUE INDEX UNIQ_orders_hash ON orders (hash)");
        $this->addSql("CREATE INDEX IDX_orders_user ON orders (user_id)");
        $this->addSql("CREATE INDEX IDX_orders_manager ON orders (manager_id)");
        $this->addSql("CREATE INDEX IDX_orders_status ON orders (status)");
        $this->addSql("CREATE INDEX IDX_orders_created ON orders (created_at)");

        $this->addSql("
            ALTER TABLE orders
            ADD CONSTRAINT FK_orders_user
            FOREIGN KEY (user_id) REFERENCES users (id)
            ON DELETE SET NULL
        ");

        $this->addSql("
            ALTER TABLE orders
            ADD CONSTRAINT FK_orders_manager
            FOREIGN KEY (manager_id) REFERENCES users (id)
            ON DELETE SET NULL
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE orders");
    }
}
