<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240820124330 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE training_attendance (id INT AUTO_INCREMENT NOT NULL, training_id INT DEFAULT NULL, player_id INT DEFAULT NULL, present SMALLINT NOT NULL, INDEX IDX_D75DB7F7BEFD98D1 (training_id), INDEX IDX_D75DB7F799E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE training_attendance ADD CONSTRAINT FK_D75DB7F7BEFD98D1 FOREIGN KEY (training_id) REFERENCES training (id)');
        $this->addSql('ALTER TABLE training_attendance ADD CONSTRAINT FK_D75DB7F799E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE training_attendance DROP FOREIGN KEY FK_D75DB7F7BEFD98D1');
        $this->addSql('ALTER TABLE training_attendance DROP FOREIGN KEY FK_D75DB7F799E6F5DF');
        $this->addSql('DROP TABLE training_attendance');
    }
}
