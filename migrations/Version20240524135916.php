<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240524135916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE games (id INT AUTO_INCREMENT NOT NULL, home_id INT DEFAULT NULL, away_id INT DEFAULT NULL, home_goals INT NOT NULL, away_goals INT NOT NULL, journee VARCHAR(255) NOT NULL, stadium VARCHAR(255) NOT NULL, INDEX IDX_FF232B3128CDC89C (home_id), INDEX IDX_FF232B318DEF089F (away_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B3128CDC89C FOREIGN KEY (home_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B318DEF089F FOREIGN KEY (away_id) REFERENCES team (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B3128CDC89C');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B318DEF089F');
        $this->addSql('DROP TABLE games');
    }
}
