<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240819131349 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_player (user_id INT NOT NULL, player_id INT NOT NULL, INDEX IDX_FD4B6158A76ED395 (user_id), INDEX IDX_FD4B615899E6F5DF (player_id), PRIMARY KEY(user_id, player_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_player ADD CONSTRAINT FK_FD4B6158A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_player ADD CONSTRAINT FK_FD4B615899E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_player DROP FOREIGN KEY FK_FD4B6158A76ED395');
        $this->addSql('ALTER TABLE user_player DROP FOREIGN KEY FK_FD4B615899E6F5DF');
        $this->addSql('DROP TABLE user_player');
    }
}
