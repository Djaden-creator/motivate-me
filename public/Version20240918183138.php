<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240918183138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE articlelike (id INT AUTO_INCREMENT NOT NULL, userid_id INT DEFAULT NULL, postid_id INT DEFAULT NULL, INDEX IDX_73177A3358E0A285 (userid_id), INDEX IDX_73177A33EB348947 (postid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE articlelike ADD CONSTRAINT FK_73177A3358E0A285 FOREIGN KEY (userid_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE articlelike ADD CONSTRAINT FK_73177A33EB348947 FOREIGN KEY (postid_id) REFERENCES article (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articlelike DROP FOREIGN KEY FK_73177A3358E0A285');
        $this->addSql('ALTER TABLE articlelike DROP FOREIGN KEY FK_73177A33EB348947');
        $this->addSql('DROP TABLE articlelike');
    }
}
