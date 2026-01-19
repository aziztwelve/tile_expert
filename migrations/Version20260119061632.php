<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119061632 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE users (
                id BIGINT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                email VARCHAR(180) NOT NULL,
                name VARCHAR(100) NOT NULL,
                role VARCHAR(50) NOT NULL,
                created_at TIMESTAMP NOT NULL
            )
        ");

        $this->addSql("CREATE UNIQUE INDEX UNIQ_users_email ON users (email)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE users");
    }
}
