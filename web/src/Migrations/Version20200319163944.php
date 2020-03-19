<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200319163944 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cours ADD periode_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9CF384C1CF FOREIGN KEY (periode_id) REFERENCES periodes (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FDCA8C9CF384C1CF ON cours (periode_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9CF384C1CF');
        $this->addSql('DROP INDEX UNIQ_FDCA8C9CF384C1CF ON cours');
        $this->addSql('ALTER TABLE cours DROP periode_id');
    }
}
