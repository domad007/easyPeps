<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200321171147 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE types (id INT AUTO_INCREMENT NOT NULL, intitule VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE competences ADD types_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE competences ADD CONSTRAINT FK_DB2077CE8EB23357 FOREIGN KEY (types_id) REFERENCES types (id)');
        $this->addSql('CREATE INDEX IDX_DB2077CE8EB23357 ON competences (types_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE competences DROP FOREIGN KEY FK_DB2077CE8EB23357');
        $this->addSql('DROP TABLE types');
        $this->addSql('DROP INDEX IDX_DB2077CE8EB23357 ON competences');
        $this->addSql('ALTER TABLE competences DROP types_id');
    }
}
