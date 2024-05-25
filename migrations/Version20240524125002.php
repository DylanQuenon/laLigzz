<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240524125002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ranking (id INT AUTO_INCREMENT NOT NULL, matches_played INT NOT NULL, wins INT NOT NULL, draws INT NOT NULL, losses INT NOT NULL, goals_scored INT NOT NULL, goals_conceded INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F296CD8AE');
        $this->addSql('DROP TABLE image');
        $this->addSql('ALTER TABLE team ADD ranking_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F20F64684 FOREIGN KEY (ranking_id) REFERENCES ranking (id)');
        $this->addSql('CREATE INDEX IDX_C4E0A61F20F64684 ON team (ranking_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61F20F64684');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, team_id INT NOT NULL, url VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, caption VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_C53D045F296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP TABLE ranking');
        $this->addSql('DROP INDEX IDX_C4E0A61F20F64684 ON team');
        $this->addSql('ALTER TABLE team DROP ranking_id');
    }
}
