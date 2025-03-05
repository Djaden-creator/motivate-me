<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241222015110 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE orderdetails (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, articleid_id INT NOT NULL, addtocartid_id INT NOT NULL, price DOUBLE PRECISION NOT NULL, quantity VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_489AFCDCA76ED395 (user_id), INDEX IDX_489AFCDC9223694D (articleid_id), INDEX IDX_489AFCDC60ED7470 (addtocartid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE orderdetails ADD CONSTRAINT FK_489AFCDCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE orderdetails ADD CONSTRAINT FK_489AFCDC9223694D FOREIGN KEY (articleid_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE orderdetails ADD CONSTRAINT FK_489AFCDC60ED7470 FOREIGN KEY (addtocartid_id) REFERENCES addtocart (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orderdetails DROP FOREIGN KEY FK_489AFCDCA76ED395');
        $this->addSql('ALTER TABLE orderdetails DROP FOREIGN KEY FK_489AFCDC9223694D');
        $this->addSql('ALTER TABLE orderdetails DROP FOREIGN KEY FK_489AFCDC60ED7470');
        $this->addSql('DROP TABLE orderdetails');
    }
}
