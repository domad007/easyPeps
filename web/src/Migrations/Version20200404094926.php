<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200404094926 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE periodes ADD semestres_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE periodes ADD CONSTRAINT FK_3F29AF80F625C1D6 FOREIGN KEY (semestres_id) REFERENCES semestres (id)');
        $this->addSql('CREATE INDEX IDX_3F29AF80F625C1D6 ON periodes (semestres_id)');
        $this->addSql('ALTER TABLE semestres DROP FOREIGN KEY FK_D9A85823F384C1CF');
        $this->addSql('DROP INDEX IDX_D9A85823F384C1CF ON semestres');
        $this->addSql('ALTER TABLE semestres DROP periode_id, DROP type');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE periodes DROP FOREIGN KEY FK_3F29AF80F625C1D6');
        $this->addSql('DROP INDEX IDX_3F29AF80F625C1D6 ON periodes');
        $this->addSql('ALTER TABLE periodes DROP semestres_id');
        $this->addSql('ALTER TABLE semestres ADD periode_id INT NOT NULL, ADD type INT NOT NULL');
        $this->addSql('ALTER TABLE semestres ADD CONSTRAINT FK_D9A85823F384C1CF FOREIGN KEY (periode_id) REFERENCES periodes (id)');
        $this->addSql('CREATE INDEX IDX_D9A85823F384C1CF ON semestres (periode_id)');
    }
}
