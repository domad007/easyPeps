<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200325153820 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE evaluation ADD periode_id INT DEFAULT NULL, DROP periode');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575F384C1CF FOREIGN KEY (periode_id) REFERENCES periodes (id)');
        $this->addSql('CREATE INDEX IDX_1323A575F384C1CF ON evaluation (periode_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575F384C1CF');
        $this->addSql('DROP INDEX IDX_1323A575F384C1CF ON evaluation');
        $this->addSql('ALTER TABLE evaluation ADD periode VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP periode_id');
    }
}
