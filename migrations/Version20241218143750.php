<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241218143750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subcart (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, subscriptionid_id INT NOT NULL, createdat DATETIME NOT NULL, INDEX IDX_D216FA3CA76ED395 (user_id), INDEX IDX_D216FA3C155DFE52 (subscriptionid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subcart ADD CONSTRAINT FK_D216FA3CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE subcart ADD CONSTRAINT FK_D216FA3C155DFE52 FOREIGN KEY (subscriptionid_id) REFERENCES subscription (id)');
        $this->addSql('ALTER TABLE subscription DROP subcart_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subcart DROP FOREIGN KEY FK_D216FA3CA76ED395');
        $this->addSql('ALTER TABLE subcart DROP FOREIGN KEY FK_D216FA3C155DFE52');
        $this->addSql('DROP TABLE subcart');
        $this->addSql('ALTER TABLE subscription ADD subcart_id INT NOT NULL');
    }
}
