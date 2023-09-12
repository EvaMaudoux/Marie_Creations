<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230820155942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendar DROP description, DROP name, DROP price, DROP max_capacity, DROP image_name');
        $this->addSql('ALTER TABLE workshop RENAME INDEX uniq_9b6f02c412469de2 TO IDX_9B6F02C412469DE2');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendar ADD description LONGTEXT NOT NULL, ADD name VARCHAR(255) NOT NULL, ADD price INT NOT NULL, ADD max_capacity INT NOT NULL, ADD image_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE workshop RENAME INDEX idx_9b6f02c412469de2 TO UNIQ_9B6F02C412469DE2');
    }
}
