<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241205155545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE like_reply (id INT AUTO_INCREMENT NOT NULL, userid_id INT NOT NULL, replyid_id INT NOT NULL, createdat DATETIME NOT NULL, INDEX IDX_4ACCEF0758E0A285 (userid_id), INDEX IDX_4ACCEF0772CF7B7A (replyid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE like_reply ADD CONSTRAINT FK_4ACCEF0758E0A285 FOREIGN KEY (userid_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE like_reply ADD CONSTRAINT FK_4ACCEF0772CF7B7A FOREIGN KEY (replyid_id) REFERENCES reply_comment (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE like_reply DROP FOREIGN KEY FK_4ACCEF0758E0A285');
        $this->addSql('ALTER TABLE like_reply DROP FOREIGN KEY FK_4ACCEF0772CF7B7A');
        $this->addSql('DROP TABLE like_reply');
    }
}
