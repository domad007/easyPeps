<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200401172341 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cours_groupe ADD customized_presences_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cours_groupe ADD CONSTRAINT FK_6B399D49BEA0D1BE FOREIGN KEY (customized_presences_id) REFERENCES customized_presences (id)');
        $this->addSql('CREATE INDEX IDX_6B399D49BEA0D1BE ON cours_groupe (customized_presences_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cours_groupe DROP FOREIGN KEY FK_6B399D49BEA0D1BE');
        $this->addSql('DROP INDEX IDX_6B399D49BEA0D1BE ON cours_groupe');
        $this->addSql('ALTER TABLE cours_groupe DROP customized_presences_id');
    }
}
