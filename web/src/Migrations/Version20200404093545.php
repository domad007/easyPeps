<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200404093545 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE semestres (id INT AUTO_INCREMENT NOT NULL, periode_id INT NOT NULL, intitule VARCHAR(255) NOT NULL, type INT NOT NULL, INDEX IDX_D9A85823F384C1CF (periode_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE semestres ADD CONSTRAINT FK_D9A85823F384C1CF FOREIGN KEY (periode_id) REFERENCES periodes (id)');
        $this->addSql('ALTER TABLE periodes ADD date_debut DATE NOT NULL, ADD date_fin DATE NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE semestres');
        $this->addSql('ALTER TABLE periodes DROP date_debut, DROP date_fin');
    }
}
