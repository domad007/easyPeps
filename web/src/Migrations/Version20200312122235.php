<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200312122235 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C82E00F4B');
        $this->addSql('ALTER TABLE eleve DROP FOREIGN KEY FK_ECA105F782E00F4B');
        $this->addSql('DROP TABLE cours_groupe');
        $this->addSql('DROP INDEX IDX_FDCA8C9C82E00F4B ON cours');
        $this->addSql('ALTER TABLE cours DROP cours_groupe_id');
        $this->addSql('DROP INDEX IDX_ECA105F782E00F4B ON eleve');
        $this->addSql('ALTER TABLE eleve DROP cours_groupe_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE cours_groupe (id INT AUTO_INCREMENT NOT NULL, points INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE cours ADD cours_groupe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C82E00F4B FOREIGN KEY (cours_groupe_id) REFERENCES cours_groupe (id)');
        $this->addSql('CREATE INDEX IDX_FDCA8C9C82E00F4B ON cours (cours_groupe_id)');
        $this->addSql('ALTER TABLE eleve ADD cours_groupe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE eleve ADD CONSTRAINT FK_ECA105F782E00F4B FOREIGN KEY (cours_groupe_id) REFERENCES cours_groupe (id)');
        $this->addSql('CREATE INDEX IDX_ECA105F782E00F4B ON eleve (cours_groupe_id)');
    }
}
