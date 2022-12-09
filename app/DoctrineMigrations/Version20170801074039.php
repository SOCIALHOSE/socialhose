<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170801074039 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE subscription ADD organization_id INT DEFAULT NULL, ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D332C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_A3C664D332C8A3DE ON subscription (organization_id)');
        $this->addSql('CREATE INDEX IDX_A3C664D3A76ED395 ON subscription (user_id)');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notifications CHANGE notification_type notification_type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE theme_type theme_type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE timezone timezone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D332C8A3DE');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D3A76ED395');
        $this->addSql('DROP INDEX IDX_A3C664D332C8A3DE ON subscription');
        $this->addSql('DROP INDEX IDX_A3C664D3A76ED395 ON subscription');
        $this->addSql('ALTER TABLE subscription DROP organization_id, DROP user_id');
    }
}
