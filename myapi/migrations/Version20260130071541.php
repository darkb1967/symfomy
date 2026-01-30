<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260130071541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE flower (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, category_id INT DEFAULT NULL, INDEX IDX_A7D7C1DA12469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE flower ADD CONSTRAINT FK_A7D7C1DA12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE bookmarks CHANGE titre titre char(50) not null');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE flower DROP FOREIGN KEY FK_A7D7C1DA12469DE2');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE flower');
        $this->addSql('ALTER TABLE bookmarks CHANGE titre titre CHAR(50) NOT NULL');
    }
}
