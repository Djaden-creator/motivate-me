<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250415215407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE likepostgroup (id INT AUTO_INCREMENT NOT NULL, userid_id INT NOT NULL, postingroupid_id INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_7544EA8D58E0A285 (userid_id), INDEX IDX_7544EA8D96C0C853 (postingroupid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE likepostgroup ADD CONSTRAINT FK_7544EA8D58E0A285 FOREIGN KEY (userid_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE likepostgroup ADD CONSTRAINT FK_7544EA8D96C0C853 FOREIGN KEY (postingroupid_id) REFERENCES shareingroup (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE likepostgroup DROP FOREIGN KEY FK_7544EA8D58E0A285');
        $this->addSql('ALTER TABLE likepostgroup DROP FOREIGN KEY FK_7544EA8D96C0C853');
        $this->addSql('DROP TABLE likepostgroup');
    }
}
