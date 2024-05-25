<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240524130056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ranking ADD team_id INT DEFAULT NULL, DROP goals_conceded');
        $this->addSql('ALTER TABLE ranking ADD CONSTRAINT FK_80B839D0296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('CREATE INDEX IDX_80B839D0296CD8AE ON ranking (team_id)');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61F20F64684');
        $this->addSql('DROP INDEX IDX_C4E0A61F20F64684 ON team');
        $this->addSql('ALTER TABLE team DROP ranking_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team ADD ranking_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F20F64684 FOREIGN KEY (ranking_id) REFERENCES ranking (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_C4E0A61F20F64684 ON team (ranking_id)');
        $this->addSql('ALTER TABLE ranking DROP FOREIGN KEY FK_80B839D0296CD8AE');
        $this->addSql('DROP INDEX IDX_80B839D0296CD8AE ON ranking');
        $this->addSql('ALTER TABLE ranking ADD goals_conceded INT NOT NULL, DROP team_id');
    }
}
