<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119061715 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE countries (
                id BIGINT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                code CHAR(2) NOT NULL,
                name VARCHAR(100) NOT NULL
            )
        ");

        $this->addSql("CREATE UNIQUE INDEX UNIQ_countries_code ON countries (code)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE countries");
    }
}
