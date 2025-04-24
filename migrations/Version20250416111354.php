<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250416111354 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commentgrouppost (id INT AUTO_INCREMENT NOT NULL, userid_id INT NOT NULL, postgroupid_id INT NOT NULL, comment LONGTEXT NOT NULL, date DATETIME NOT NULL, INDEX IDX_A7F7BB6858E0A285 (userid_id), INDEX IDX_A7F7BB68E7C96AD6 (postgroupid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentgrouppost ADD CONSTRAINT FK_A7F7BB6858E0A285 FOREIGN KEY (userid_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commentgrouppost ADD CONSTRAINT FK_A7F7BB68E7C96AD6 FOREIGN KEY (postgroupid_id) REFERENCES shareingroup (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentgrouppost DROP FOREIGN KEY FK_A7F7BB6858E0A285');
        $this->addSql('ALTER TABLE commentgrouppost DROP FOREIGN KEY FK_A7F7BB68E7C96AD6');
        $this->addSql('DROP TABLE commentgrouppost');
    }
}
