<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200322114549 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE cours_evaluation');
        $this->addSql('ALTER TABLE evaluation ADD cours_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A5757ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('CREATE INDEX IDX_1323A5757ECF78B0 ON evaluation (cours_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE cours_evaluation (cours_id INT NOT NULL, evaluation_id INT NOT NULL, INDEX IDX_16DB575C456C5646 (evaluation_id), INDEX IDX_16DB575C7ECF78B0 (cours_id), PRIMARY KEY(cours_id, evaluation_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE cours_evaluation ADD CONSTRAINT FK_16DB575C456C5646 FOREIGN KEY (evaluation_id) REFERENCES evaluation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cours_evaluation ADD CONSTRAINT FK_16DB575C7ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A5757ECF78B0');
        $this->addSql('DROP INDEX IDX_1323A5757ECF78B0 ON evaluation');
        $this->addSql('ALTER TABLE evaluation DROP cours_id');
    }
}
