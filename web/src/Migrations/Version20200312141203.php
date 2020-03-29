<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200312141203 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE evaluation_groupe (id INT AUTO_INCREMENT NOT NULL, evaluation_id_id INT DEFAULT NULL, eleve_id_id INT DEFAULT NULL, points INT NOT NULL, INDEX IDX_CEC6B9D6BAB3E3A6 (evaluation_id_id), INDEX IDX_CEC6B9D6602483BE (eleve_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE evaluation_groupe ADD CONSTRAINT FK_CEC6B9D6BAB3E3A6 FOREIGN KEY (evaluation_id_id) REFERENCES evaluation (id)');
        $this->addSql('ALTER TABLE evaluation_groupe ADD CONSTRAINT FK_CEC6B9D6602483BE FOREIGN KEY (eleve_id_id) REFERENCES eleve (id)');
        $this->addSql('ALTER TABLE evaluation ADD groupe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A5757A45358C FOREIGN KEY (groupe_id) REFERENCES groups (id)');
        $this->addSql('CREATE INDEX IDX_1323A5757A45358C ON evaluation (groupe_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE evaluation_groupe');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A5757A45358C');
        $this->addSql('DROP INDEX IDX_1323A5757A45358C ON evaluation');
        $this->addSql('ALTER TABLE evaluation DROP groupe_id');
    }
}
