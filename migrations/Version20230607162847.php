<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230607162847 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE creation DROP FOREIGN KEY FK_57EE85741A4CE194');
        $this->addSql('DROP INDEX IDX_57EE85741A4CE194 ON creation');
        $this->addSql('ALTER TABLE creation CHANGE category_c_id category_id INT NOT NULL');
        $this->addSql('ALTER TABLE creation ADD CONSTRAINT FK_57EE857412469DE2 FOREIGN KEY (category_id) REFERENCES category_art (id)');
        $this->addSql('CREATE INDEX IDX_57EE857412469DE2 ON creation (category_id)');
        $this->addSql('ALTER TABLE workshop CHANGE description description LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE creation DROP FOREIGN KEY FK_57EE857412469DE2');
        $this->addSql('DROP INDEX IDX_57EE857412469DE2 ON creation');
        $this->addSql('ALTER TABLE creation CHANGE category_id category_c_id INT NOT NULL');
        $this->addSql('ALTER TABLE creation ADD CONSTRAINT FK_57EE85741A4CE194 FOREIGN KEY (category_c_id) REFERENCES category_art (id)');
        $this->addSql('CREATE INDEX IDX_57EE85741A4CE194 ON creation (category_c_id)');
        $this->addSql('ALTER TABLE workshop CHANGE description description TEXT NOT NULL');
    }
}
