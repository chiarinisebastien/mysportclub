<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240724090859 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE player_category (player_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_9B582199E6F5DF (player_id), INDEX IDX_9B582112469DE2 (category_id), PRIMARY KEY(player_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE player_category ADD CONSTRAINT FK_9B582199E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player_category ADD CONSTRAINT FK_9B582112469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player_category DROP FOREIGN KEY FK_9B582199E6F5DF');
        $this->addSql('ALTER TABLE player_category DROP FOREIGN KEY FK_9B582112469DE2');
        $this->addSql('DROP TABLE player_category');
    }
}
