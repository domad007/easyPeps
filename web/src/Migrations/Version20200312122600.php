<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200312122600 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE cours_groupe (id INT AUTO_INCREMENT NOT NULL, cours_id_id INT DEFAULT NULL, eleve_id_id INT DEFAULT NULL, INDEX IDX_6B399D494F221781 (cours_id_id), INDEX IDX_6B399D49602483BE (eleve_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cours_groupe ADD CONSTRAINT FK_6B399D494F221781 FOREIGN KEY (cours_id_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE cours_groupe ADD CONSTRAINT FK_6B399D49602483BE FOREIGN KEY (eleve_id_id) REFERENCES eleve (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE cours_groupe');
    }
}
