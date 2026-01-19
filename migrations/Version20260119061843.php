<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119061843 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE articles (
                id BIGINT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                sku VARCHAR(50) NOT NULL,
                name VARCHAR(200) NOT NULL,
                weight NUMERIC(10,3) NOT NULL
            )
        ");

        $this->addSql("CREATE UNIQUE INDEX UNIQ_articles_sku ON articles (sku)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE articles");
    }
}
