<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240503101631 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reading ADD station_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reading ADD CONSTRAINT FK_C11AFC4121BDB235 FOREIGN KEY (station_id) REFERENCES station (id)');
        $this->addSql('CREATE INDEX IDX_C11AFC4121BDB235 ON reading (station_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reading DROP FOREIGN KEY FK_C11AFC4121BDB235');
        $this->addSql('DROP INDEX IDX_C11AFC4121BDB235 ON reading');
        $this->addSql('ALTER TABLE reading DROP station_id');
    }
}
