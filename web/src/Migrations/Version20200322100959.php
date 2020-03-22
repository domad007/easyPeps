<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200322100959 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575A660B158');
        $this->addSql('ALTER TABLE competences DROP FOREIGN KEY FK_DB2077CE659BAA9E');
        $this->addSql('DROP TABLE competences');
        $this->addSql('DROP TABLE types');
        $this->addSql('DROP INDEX IDX_1323A575A660B158 ON evaluation');
        $this->addSql('ALTER TABLE evaluation DROP competences_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE competences (id INT AUTO_INCREMENT NOT NULL, type_competence_id INT DEFAULT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, degre INT NOT NULL, INDEX IDX_DB2077CE659BAA9E (type_competence_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE types (id INT AUTO_INCREMENT NOT NULL, intitule VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE competences ADD CONSTRAINT FK_DB2077CE659BAA9E FOREIGN KEY (type_competence_id) REFERENCES types (id)');
        $this->addSql('ALTER TABLE evaluation ADD competences_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575A660B158 FOREIGN KEY (competences_id) REFERENCES competences (id)');
        $this->addSql('CREATE INDEX IDX_1323A575A660B158 ON evaluation (competences_id)');
    }
}
