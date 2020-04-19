<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200411131253 extends AbstractMigration
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
        $this->addSql('CREATE TABLE competences (id INT AUTO_INCREMENT NOT NULL, type_competence_id INT DEFAULT NULL, degre_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_DB2077CE659BAA9E (type_competence_id), INDEX IDX_DB2077CEEB168534 (degre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, groupe_id INT DEFAULT NULL, periode_id INT DEFAULT NULL, intitule VARCHAR(255) NOT NULL, date_cours DATE NOT NULL, nombre_heures VARCHAR(255) NOT NULL, sur_combien VARCHAR(255) NOT NULL, INDEX IDX_FDCA8C9C7A45358C (groupe_id), INDEX IDX_FDCA8C9CF384C1CF (periode_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cours_groupe (id INT AUTO_INCREMENT NOT NULL, cours_id_id INT DEFAULT NULL, eleve_id_id INT DEFAULT NULL, presences_id INT DEFAULT NULL, customized_presences_id INT DEFAULT NULL, points VARCHAR(255) NOT NULL, INDEX IDX_6B399D494F221781 (cours_id_id), INDEX IDX_6B399D49602483BE (eleve_id_id), INDEX IDX_6B399D497B8B9373 (presences_id), INDEX IDX_6B399D49BEA0D1BE (customized_presences_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customized_presences (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, type_presence_id INT DEFAULT NULL, abreviation_customized VARCHAR(255) NOT NULL, INDEX IDX_15E4C5BBA76ED395 (user_id), INDEX IDX_15E4C5BBB8312855 (type_presence_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE degre (id INT AUTO_INCREMENT NOT NULL, intitule VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ecole (id INT AUTO_INCREMENT NOT NULL, nom_ecole VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE eleve (id INT AUTO_INCREMENT NOT NULL, classe_id INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, date_naissance DATE NOT NULL, INDEX IDX_ECA105F78F5EA509 (classe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE eleve_supprime (id INT AUTO_INCREMENT NOT NULL, classe_id INT NOT NULL, ecole_id INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, date_naissance DATE NOT NULL, INDEX IDX_740BA5698F5EA509 (classe_id), INDEX IDX_740BA56977EF1B1E (ecole_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evaluation (id INT AUTO_INCREMENT NOT NULL, competence_id INT DEFAULT NULL, groupe_id INT DEFAULT NULL, periode_id INT DEFAULT NULL, intitule VARCHAR(255) NOT NULL, date_evaluation DATE NOT NULL, heures_competence VARCHAR(255) NOT NULL, sur_combien VARCHAR(255) NOT NULL, INDEX IDX_1323A57515761DAB (competence_id), INDEX IDX_1323A5757A45358C (groupe_id), INDEX IDX_1323A575F384C1CF (periode_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evaluation_group (id INT AUTO_INCREMENT NOT NULL, evaluation_id INT DEFAULT NULL, eleve_id INT DEFAULT NULL, points VARCHAR(255) NOT NULL, INDEX IDX_A8373934456C5646 (evaluation_id), INDEX IDX_A8373934A6CC7B2 (eleve_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE groups (id INT AUTO_INCREMENT NOT NULL, degre_id INT DEFAULT NULL, professeur_id INT DEFAULT NULL, INDEX IDX_F06D3970EB168534 (degre_id), INDEX IDX_F06D3970BAB22EE9 (professeur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE periodes (id INT AUTO_INCREMENT NOT NULL, groupe_id INT DEFAULT NULL, semestres_id INT DEFAULT NULL, nom_periode VARCHAR(255) NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, INDEX IDX_3F29AF807A45358C (groupe_id), INDEX IDX_3F29AF80F625C1D6 (semestres_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE presences (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, abreviation VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role_user (role_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_332CA4DDD60322AC (role_id), INDEX IDX_332CA4DDA76ED395 (user_id), PRIMARY KEY(role_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE semestres (id INT AUTO_INCREMENT NOT NULL, intitule VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE types (id INT AUTO_INCREMENT NOT NULL, intitule VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, nom_user VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, mdp VARCHAR(255) NOT NULL, sexe VARCHAR(30) NOT NULL, date_naiss DATE NOT NULL, reset_token VARCHAR(255) DEFAULT NULL, user_actif TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF96BAB22EE9 FOREIGN KEY (professeur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF9677EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id)');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF96F373DCF FOREIGN KEY (groups_id) REFERENCES groups (id)');
        $this->addSql('ALTER TABLE competences ADD CONSTRAINT FK_DB2077CE659BAA9E FOREIGN KEY (type_competence_id) REFERENCES types (id)');
        $this->addSql('ALTER TABLE competences ADD CONSTRAINT FK_DB2077CEEB168534 FOREIGN KEY (degre_id) REFERENCES degre (id)');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C7A45358C FOREIGN KEY (groupe_id) REFERENCES groups (id)');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9CF384C1CF FOREIGN KEY (periode_id) REFERENCES periodes (id)');
        $this->addSql('ALTER TABLE cours_groupe ADD CONSTRAINT FK_6B399D494F221781 FOREIGN KEY (cours_id_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE cours_groupe ADD CONSTRAINT FK_6B399D49602483BE FOREIGN KEY (eleve_id_id) REFERENCES eleve (id)');
        $this->addSql('ALTER TABLE cours_groupe ADD CONSTRAINT FK_6B399D497B8B9373 FOREIGN KEY (presences_id) REFERENCES presences (id)');
        $this->addSql('ALTER TABLE cours_groupe ADD CONSTRAINT FK_6B399D49BEA0D1BE FOREIGN KEY (customized_presences_id) REFERENCES customized_presences (id)');
        $this->addSql('ALTER TABLE customized_presences ADD CONSTRAINT FK_15E4C5BBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE customized_presences ADD CONSTRAINT FK_15E4C5BBB8312855 FOREIGN KEY (type_presence_id) REFERENCES presences (id)');
        $this->addSql('ALTER TABLE eleve ADD CONSTRAINT FK_ECA105F78F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE eleve_supprime ADD CONSTRAINT FK_740BA5698F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE eleve_supprime ADD CONSTRAINT FK_740BA56977EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A57515761DAB FOREIGN KEY (competence_id) REFERENCES competences (id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A5757A45358C FOREIGN KEY (groupe_id) REFERENCES groups (id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575F384C1CF FOREIGN KEY (periode_id) REFERENCES periodes (id)');
        $this->addSql('ALTER TABLE evaluation_group ADD CONSTRAINT FK_A8373934456C5646 FOREIGN KEY (evaluation_id) REFERENCES evaluation (id)');
        $this->addSql('ALTER TABLE evaluation_group ADD CONSTRAINT FK_A8373934A6CC7B2 FOREIGN KEY (eleve_id) REFERENCES eleve (id)');
        $this->addSql('ALTER TABLE groups ADD CONSTRAINT FK_F06D3970EB168534 FOREIGN KEY (degre_id) REFERENCES degre (id)');
        $this->addSql('ALTER TABLE groups ADD CONSTRAINT FK_F06D3970BAB22EE9 FOREIGN KEY (professeur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE periodes ADD CONSTRAINT FK_3F29AF807A45358C FOREIGN KEY (groupe_id) REFERENCES groups (id)');
        $this->addSql('ALTER TABLE periodes ADD CONSTRAINT FK_3F29AF80F625C1D6 FOREIGN KEY (semestres_id) REFERENCES semestres (id)');
        $this->addSql('ALTER TABLE role_user ADD CONSTRAINT FK_332CA4DDD60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_user ADD CONSTRAINT FK_332CA4DDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE eleve DROP FOREIGN KEY FK_ECA105F78F5EA509');
        $this->addSql('ALTER TABLE eleve_supprime DROP FOREIGN KEY FK_740BA5698F5EA509');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A57515761DAB');
        $this->addSql('ALTER TABLE cours_groupe DROP FOREIGN KEY FK_6B399D494F221781');
        $this->addSql('ALTER TABLE cours_groupe DROP FOREIGN KEY FK_6B399D49BEA0D1BE');
        $this->addSql('ALTER TABLE competences DROP FOREIGN KEY FK_DB2077CEEB168534');
        $this->addSql('ALTER TABLE groups DROP FOREIGN KEY FK_F06D3970EB168534');
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF9677EF1B1E');
        $this->addSql('ALTER TABLE eleve_supprime DROP FOREIGN KEY FK_740BA56977EF1B1E');
        $this->addSql('ALTER TABLE cours_groupe DROP FOREIGN KEY FK_6B399D49602483BE');
        $this->addSql('ALTER TABLE evaluation_group DROP FOREIGN KEY FK_A8373934A6CC7B2');
        $this->addSql('ALTER TABLE evaluation_group DROP FOREIGN KEY FK_A8373934456C5646');
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF96F373DCF');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C7A45358C');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A5757A45358C');
        $this->addSql('ALTER TABLE periodes DROP FOREIGN KEY FK_3F29AF807A45358C');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9CF384C1CF');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575F384C1CF');
        $this->addSql('ALTER TABLE cours_groupe DROP FOREIGN KEY FK_6B399D497B8B9373');
        $this->addSql('ALTER TABLE customized_presences DROP FOREIGN KEY FK_15E4C5BBB8312855');
        $this->addSql('ALTER TABLE role_user DROP FOREIGN KEY FK_332CA4DDD60322AC');
        $this->addSql('ALTER TABLE periodes DROP FOREIGN KEY FK_3F29AF80F625C1D6');
        $this->addSql('ALTER TABLE competences DROP FOREIGN KEY FK_DB2077CE659BAA9E');
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF96BAB22EE9');
        $this->addSql('ALTER TABLE customized_presences DROP FOREIGN KEY FK_15E4C5BBA76ED395');
        $this->addSql('ALTER TABLE groups DROP FOREIGN KEY FK_F06D3970BAB22EE9');
        $this->addSql('ALTER TABLE role_user DROP FOREIGN KEY FK_332CA4DDA76ED395');
        $this->addSql('DROP TABLE classe');
        $this->addSql('DROP TABLE competences');
        $this->addSql('DROP TABLE cours');
        $this->addSql('DROP TABLE cours_groupe');
        $this->addSql('DROP TABLE customized_presences');
        $this->addSql('DROP TABLE degre');
        $this->addSql('DROP TABLE ecole');
        $this->addSql('DROP TABLE eleve');
        $this->addSql('DROP TABLE eleve_supprime');
        $this->addSql('DROP TABLE evaluation');
        $this->addSql('DROP TABLE evaluation_group');
        $this->addSql('DROP TABLE groups');
        $this->addSql('DROP TABLE periodes');
        $this->addSql('DROP TABLE presences');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE role_user');
        $this->addSql('DROP TABLE semestres');
        $this->addSql('DROP TABLE types');
        $this->addSql('DROP TABLE user');
    }
}
