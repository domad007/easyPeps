<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200325152846 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE evaluation (id INT AUTO_INCREMENT NOT NULL, competence_id INT DEFAULT NULL, groupe_id INT DEFAULT NULL, intitule VARCHAR(255) NOT NULL, date_evaluation DATE NOT NULL, heures_competence VARCHAR(255) NOT NULL, periode VARCHAR(255) NOT NULL, INDEX IDX_1323A57515761DAB (competence_id), INDEX IDX_1323A5757A45358C (groupe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evaluation_group (id INT AUTO_INCREMENT NOT NULL, evaluation_id INT DEFAULT NULL, eleve_id INT DEFAULT NULL, points VARCHAR(255) NOT NULL, INDEX IDX_A8373934456C5646 (evaluation_id), INDEX IDX_A8373934A6CC7B2 (eleve_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A57515761DAB FOREIGN KEY (competence_id) REFERENCES competences (id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A5757A45358C FOREIGN KEY (groupe_id) REFERENCES groups (id)');
        $this->addSql('ALTER TABLE evaluation_group ADD CONSTRAINT FK_A8373934456C5646 FOREIGN KEY (evaluation_id) REFERENCES evaluation (id)');
        $this->addSql('ALTER TABLE evaluation_group ADD CONSTRAINT FK_A8373934A6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE evaluation_group DROP FOREIGN KEY FK_A8373934456C5646');
        $this->addSql('DROP TABLE evaluation');
        $this->addSql('DROP TABLE evaluation_group');
    }
}
