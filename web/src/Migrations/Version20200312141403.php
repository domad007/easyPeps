<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200312141403 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE evaluation_groupe DROP FOREIGN KEY FK_CEC6B9D6BAB3E3A6');
        $this->addSql('DROP TABLE evaluation');
        $this->addSql('DROP TABLE evaluation_groupe');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE evaluation (id INT AUTO_INCREMENT NOT NULL, groupe_id INT DEFAULT NULL, date_evaluation DATE NOT NULL, intitule VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_1323A5757A45358C (groupe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE evaluation_groupe (id INT AUTO_INCREMENT NOT NULL, evaluation_id_id INT DEFAULT NULL, eleve_id_id INT DEFAULT NULL, points INT NOT NULL, INDEX IDX_CEC6B9D6BAB3E3A6 (evaluation_id_id), INDEX IDX_CEC6B9D6602483BE (eleve_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A5757A45358C FOREIGN KEY (groupe_id) REFERENCES groups (id)');
        $this->addSql('ALTER TABLE evaluation_groupe ADD CONSTRAINT FK_CEC6B9D6602483BE FOREIGN KEY (eleve_id_id) REFERENCES eleve (id)');
        $this->addSql('ALTER TABLE evaluation_groupe ADD CONSTRAINT FK_CEC6B9D6BAB3E3A6 FOREIGN KEY (evaluation_id_id) REFERENCES evaluation (id)');
    }
}
