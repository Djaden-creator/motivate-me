<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250424145748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification ADD shareingroupid_id INT NOT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA863C0C1 FOREIGN KEY (shareingroupid_id) REFERENCES shareingroup (id)');
        $this->addSql('CREATE INDEX IDX_BF5476CA863C0C1 ON notification (shareingroupid_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA863C0C1');
        $this->addSql('DROP INDEX IDX_BF5476CA863C0C1 ON notification');
        $this->addSql('ALTER TABLE notification DROP shareingroupid_id');
    }
}
