<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200305194033 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE eleve_supprime ADD ecole_id INT NOT NULL');
        $this->addSql('ALTER TABLE eleve_supprime ADD CONSTRAINT FK_740BA56977EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id)');
        $this->addSql('CREATE INDEX IDX_740BA56977EF1B1E ON eleve_supprime (ecole_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE eleve_supprime DROP FOREIGN KEY FK_740BA56977EF1B1E');
        $this->addSql('DROP INDEX IDX_740BA56977EF1B1E ON eleve_supprime');
        $this->addSql('ALTER TABLE eleve_supprime DROP ecole_id');
    }
}
