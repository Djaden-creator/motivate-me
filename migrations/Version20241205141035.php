<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241205141035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reply_comment (id INT AUTO_INCREMENT NOT NULL, userid_id INT NOT NULL, comment_id INT NOT NULL, descriptionreply LONGTEXT NOT NULL, created DATETIME NOT NULL, INDEX IDX_89CA3BAE58E0A285 (userid_id), INDEX IDX_89CA3BAEF8697D13 (comment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reply_comment ADD CONSTRAINT FK_89CA3BAE58E0A285 FOREIGN KEY (userid_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reply_comment ADD CONSTRAINT FK_89CA3BAEF8697D13 FOREIGN KEY (comment_id) REFERENCES commentaires (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reply_comment DROP FOREIGN KEY FK_89CA3BAE58E0A285');
        $this->addSql('ALTER TABLE reply_comment DROP FOREIGN KEY FK_89CA3BAEF8697D13');
        $this->addSql('DROP TABLE reply_comment');
    }
}
