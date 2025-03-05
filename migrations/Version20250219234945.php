<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219234945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE motivateur (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, titre VARCHAR(255) NOT NULL, leader_name VARCHAR(255) NOT NULL, etablissement VARCHAR(255) NOT NULL, religion VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, sellernumber VARCHAR(255) NOT NULL, picture VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_4D562C1DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE motivateur ADD CONSTRAINT FK_4D562C1DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE motivateur DROP FOREIGN KEY FK_4D562C1DA76ED395');
        $this->addSql('DROP TABLE motivateur');
    }
}
