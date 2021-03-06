<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191008084437 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create table `client`';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE TABLE client (
                id CHAR(36) NOT NULL, 
                name VARCHAR(128) NOT NULL, 
                secret VARCHAR(128) NOT NULL, 
                redirect JSON NOT NULL COMMENT \'(DC2Type:json_array)\', 
                grants JSON NOT NULL COMMENT \'(DC2Type:json_array)\', 
                confidential TINYINT(1) NOT NULL, 
                active TINYINT(1) NOT NULL, 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE client');
    }
}
