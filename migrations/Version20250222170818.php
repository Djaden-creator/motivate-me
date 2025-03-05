<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250222170818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE motivateur ADD codeseller_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE motivateur ADD CONSTRAINT FK_4D562C1D5AA91D92 FOREIGN KEY (codeseller_id) REFERENCES generatorcode (id)');
        $this->addSql('CREATE INDEX IDX_4D562C1D5AA91D92 ON motivateur (codeseller_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE motivateur DROP FOREIGN KEY FK_4D562C1D5AA91D92');
        $this->addSql('DROP INDEX IDX_4D562C1D5AA91D92 ON motivateur');
        $this->addSql('ALTER TABLE motivateur DROP codeseller_id');
    }
}
