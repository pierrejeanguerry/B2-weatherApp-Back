<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240503122807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reading CHANGE station_id station_id INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F39F8B11713EB65 ON station (mac)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reading CHANGE station_id station_id INT DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_9F39F8B11713EB65 ON station');
    }
}
