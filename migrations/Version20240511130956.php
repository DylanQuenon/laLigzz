<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240511130956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE news_team (news_id INT NOT NULL, team_id INT NOT NULL, INDEX IDX_113D5094B5A459A0 (news_id), INDEX IDX_113D5094296CD8AE (team_id), PRIMARY KEY(news_id, team_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE news_team ADD CONSTRAINT FK_113D5094B5A459A0 FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE news_team ADD CONSTRAINT FK_113D5094296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE news_team DROP FOREIGN KEY FK_113D5094B5A459A0');
        $this->addSql('ALTER TABLE news_team DROP FOREIGN KEY FK_113D5094296CD8AE');
        $this->addSql('DROP TABLE news_team');
    }
}
