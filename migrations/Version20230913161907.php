<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230913161907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription_notif DROP FOREIGN KEY FK_3A4B0C18A76ED395');
        $this->addSql('DROP INDEX IDX_3A4B0C18A76ED395 ON subscription_notif');
        $this->addSql('ALTER TABLE subscription_notif CHANGE user_id subscrible_user_id INT NOT NULL');
        $this->addSql('ALTER TABLE subscription_notif ADD CONSTRAINT FK_3A4B0C1881536B60 FOREIGN KEY (subscrible_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_3A4B0C1881536B60 ON subscription_notif (subscrible_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription_notif DROP FOREIGN KEY FK_3A4B0C1881536B60');
        $this->addSql('DROP INDEX IDX_3A4B0C1881536B60 ON subscription_notif');
        $this->addSql('ALTER TABLE subscription_notif CHANGE subscrible_user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE subscription_notif ADD CONSTRAINT FK_3A4B0C18A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_3A4B0C18A76ED395 ON subscription_notif (user_id)');
    }
}
