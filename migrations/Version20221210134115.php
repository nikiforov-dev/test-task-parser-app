<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221210134115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '0001. First migration';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) DEFAULT NULL, hash VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, content LONGTEXT NOT NULL, image_url VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, UNIQUE INDEX UNIQ_23A0E66D1B862B8 (hash), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE example_table');
        $this->addSql('ALTER TABLE article CHANGE image_url image_url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE example_table (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE article');
    }
}
