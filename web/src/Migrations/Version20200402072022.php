<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200402072022 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customized_presences DROP INDEX UNIQ_15E4C5BBB8312855, ADD INDEX IDX_15E4C5BBB8312855 (type_presence_id)');
        $this->addSql('ALTER TABLE customized_presences CHANGE type_presence_id type_presence_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customized_presences DROP INDEX IDX_15E4C5BBB8312855, ADD UNIQUE INDEX UNIQ_15E4C5BBB8312855 (type_presence_id)');
        $this->addSql('ALTER TABLE customized_presences CHANGE type_presence_id type_presence_id INT NOT NULL');
    }
}
