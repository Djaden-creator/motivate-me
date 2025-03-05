<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241205174300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE follower (id INT AUTO_INCREMENT NOT NULL, sessionuser_id INT NOT NULL, offsessionuser_id INT NOT NULL, friendship VARCHAR(255) NOT NULL, INDEX IDX_B9D6094631363E4F (sessionuser_id), UNIQUE INDEX UNIQ_B9D609469611A79C (offsessionuser_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE follower ADD CONSTRAINT FK_B9D6094631363E4F FOREIGN KEY (sessionuser_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE follower ADD CONSTRAINT FK_B9D609469611A79C FOREIGN KEY (offsessionuser_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE follower DROP FOREIGN KEY FK_B9D6094631363E4F');
        $this->addSql('ALTER TABLE follower DROP FOREIGN KEY FK_B9D609469611A79C');
        $this->addSql('DROP TABLE follower');
    }
}
