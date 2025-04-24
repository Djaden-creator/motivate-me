<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250416132624 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE votecomment (id INT AUTO_INCREMENT NOT NULL, userid_id INT NOT NULL, commentpostingroupid_id INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_61A0C7B558E0A285 (userid_id), INDEX IDX_61A0C7B59AB16BA (commentpostingroupid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE votecomment ADD CONSTRAINT FK_61A0C7B558E0A285 FOREIGN KEY (userid_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE votecomment ADD CONSTRAINT FK_61A0C7B59AB16BA FOREIGN KEY (commentpostingroupid_id) REFERENCES commentgrouppost (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE votecomment DROP FOREIGN KEY FK_61A0C7B558E0A285');
        $this->addSql('ALTER TABLE votecomment DROP FOREIGN KEY FK_61A0C7B59AB16BA');
        $this->addSql('DROP TABLE votecomment');
    }
}
