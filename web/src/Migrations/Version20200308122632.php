<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200308122632 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE classe (id INT AUTO_INCREMENT NOT NULL, professeur_id INT DEFAULT NULL, ecole_id INT NOT NULL, groups_id INT DEFAULT NULL, nom_classe VARCHAR(255) NOT NULL, titulaire VARCHAR(255) NOT NULL, INDEX IDX_8F87BF96BAB22EE9 (professeur_id), INDEX IDX_8F87BF9677EF1B1E (ecole_id), INDEX IDX_8F87BF96F373DCF (groups_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ecole (id INT AUTO_INCREMENT NOT NULL, nom_ecole VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE eleve (id INT AUTO_INCREMENT NOT NULL, classe_id INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, date_naissance DATE NOT NULL, INDEX IDX_ECA105F78F5EA509 (classe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE eleve_supprime (id INT AUTO_INCREMENT NOT NULL, classe_id INT NOT NULL, ecole_id INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, date_naissance DATE NOT NULL, INDEX IDX_740BA5698F5EA509 (classe_id), INDEX IDX_740BA56977EF1B1E (ecole_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE groups (id INT AUTO_INCREMENT NOT NULL, nom_group VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, nom_user VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, mdp VARCHAR(255) NOT NULL, sexe VARCHAR(30) NOT NULL, date_naiss DATE NOT NULL, reset_token VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF96BAB22EE9 FOREIGN KEY (professeur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF9677EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id)');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF96F373DCF FOREIGN KEY (groups_id) REFERENCES groups (id)');
        $this->addSql('ALTER TABLE eleve ADD CONSTRAINT FK_ECA105F78F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE eleve_supprime ADD CONSTRAINT FK_740BA5698F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE eleve_supprime ADD CONSTRAINT FK_740BA56977EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE eleve DROP FOREIGN KEY FK_ECA105F78F5EA509');
        $this->addSql('ALTER TABLE eleve_supprime DROP FOREIGN KEY FK_740BA5698F5EA509');
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF9677EF1B1E');
        $this->addSql('ALTER TABLE eleve_supprime DROP FOREIGN KEY FK_740BA56977EF1B1E');
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF96F373DCF');
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF96BAB22EE9');
        $this->addSql('DROP TABLE classe');
        $this->addSql('DROP TABLE ecole');
        $this->addSql('DROP TABLE eleve');
        $this->addSql('DROP TABLE eleve_supprime');
        $this->addSql('DROP TABLE groups');
        $this->addSql('DROP TABLE user');
    }
}
