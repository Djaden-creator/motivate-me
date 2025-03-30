<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250330003044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE shareingroup (id INT AUTO_INCREMENT NOT NULL, groupid_id INT NOT NULL, posterownerid_id INT NOT NULL, description LONGTEXT NOT NULL, image VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_3D5F1C10B3BB53C (groupid_id), INDEX IDX_3D5F1C1088DAEB3E (posterownerid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shareingroup ADD CONSTRAINT FK_3D5F1C10B3BB53C FOREIGN KEY (groupid_id) REFERENCES `groups` (id)');
        $this->addSql('ALTER TABLE shareingroup ADD CONSTRAINT FK_3D5F1C1088DAEB3E FOREIGN KEY (posterownerid_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shareingroup DROP FOREIGN KEY FK_3D5F1C10B3BB53C');
        $this->addSql('ALTER TABLE shareingroup DROP FOREIGN KEY FK_3D5F1C1088DAEB3E');
        $this->addSql('DROP TABLE shareingroup');
    }
}
