<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250327151750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE addingroup (id INT AUTO_INCREMENT NOT NULL, addedby_id INT NOT NULL, newmember_id INT NOT NULL, groupid_id INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_FB3AD15AEAF80C6 (addedby_id), INDEX IDX_FB3AD151C3E1AB2 (newmember_id), INDEX IDX_FB3AD15B3BB53C (groupid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE addingroup ADD CONSTRAINT FK_FB3AD15AEAF80C6 FOREIGN KEY (addedby_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE addingroup ADD CONSTRAINT FK_FB3AD151C3E1AB2 FOREIGN KEY (newmember_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE addingroup ADD CONSTRAINT FK_FB3AD15B3BB53C FOREIGN KEY (groupid_id) REFERENCES `groups` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE addingroup DROP FOREIGN KEY FK_FB3AD15AEAF80C6');
        $this->addSql('ALTER TABLE addingroup DROP FOREIGN KEY FK_FB3AD151C3E1AB2');
        $this->addSql('ALTER TABLE addingroup DROP FOREIGN KEY FK_FB3AD15B3BB53C');
        $this->addSql('DROP TABLE addingroup');
    }
}
