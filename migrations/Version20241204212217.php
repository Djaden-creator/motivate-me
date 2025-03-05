<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241204212217 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment_like (id INT AUTO_INCREMENT NOT NULL, commentid_id INT NOT NULL, userid_id INT NOT NULL, createdat DATETIME NOT NULL, INDEX IDX_8A55E25F4574CA0 (commentid_id), INDEX IDX_8A55E25F58E0A285 (userid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment_like ADD CONSTRAINT FK_8A55E25F4574CA0 FOREIGN KEY (commentid_id) REFERENCES commentaires (id)');
        $this->addSql('ALTER TABLE comment_like ADD CONSTRAINT FK_8A55E25F58E0A285 FOREIGN KEY (userid_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment_like DROP FOREIGN KEY FK_8A55E25F4574CA0');
        $this->addSql('ALTER TABLE comment_like DROP FOREIGN KEY FK_8A55E25F58E0A285');
        $this->addSql('DROP TABLE comment_like');
    }
}
