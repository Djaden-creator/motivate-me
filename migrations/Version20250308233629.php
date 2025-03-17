<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250308233629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE following (id INT AUTO_INCREMENT NOT NULL, usersessionid_id INT NOT NULL, offuserid_id INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_71BF8DE313B6950D (usersessionid_id), INDEX IDX_71BF8DE3FB62AB71 (offuserid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE following ADD CONSTRAINT FK_71BF8DE313B6950D FOREIGN KEY (usersessionid_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE following ADD CONSTRAINT FK_71BF8DE3FB62AB71 FOREIGN KEY (offuserid_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE following DROP FOREIGN KEY FK_71BF8DE313B6950D');
        $this->addSql('ALTER TABLE following DROP FOREIGN KEY FK_71BF8DE3FB62AB71');
        $this->addSql('DROP TABLE following');
    }
}
