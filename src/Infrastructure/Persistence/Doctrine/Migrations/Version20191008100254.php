<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191008100254 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create table `access_token`';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE TABLE access_token (
                id VARCHAR(255) NOT NULL, 
                user_id VARCHAR(255) DEFAULT NULL, 
                client_id VARCHAR(255) NOT NULL, 
                scopes JSON NOT NULL COMMENT \'(DC2Type:json_array)\', 
                revoked TINYINT(1) NOT NULL, 
                created_at DATETIME NOT NULL, 
                updated_at DATETIME NOT NULL, 
                expires_at DATETIME NOT NULL, 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE access_token');
    }
}
