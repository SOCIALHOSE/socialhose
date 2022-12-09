<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170801102300 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users DROP private_person');
        $this->addSql('ALTER TABLE subscription ADD organization_address VARCHAR(255) NOT NULL, ADD organization_email VARCHAR(255) NOT NULL, ADD organization_phone VARCHAR(255) NOT NULL, DROP type');
        $this->addSql('ALTER TABLE notifications CHANGE timezone timezone VARCHAR(255) NOT NULL, CHANGE notification_type notification_type VARCHAR(255) NOT NULL, CHANGE theme_type theme_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE plan DROP limitPlan');
        $this->addSql('ALTER TABLE organizations DROP organization_address, DROP organization_email, DROP organization_phone');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notifications CHANGE notification_type notification_type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE theme_type theme_type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE timezone timezone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE organizations ADD organization_address VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD organization_email VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD organization_phone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE plan ADD limitPlan INT NOT NULL');
        $this->addSql('ALTER TABLE subscription ADD type INT NOT NULL, DROP organization_address, DROP organization_email, DROP organization_phone');
        $this->addSql('ALTER TABLE users ADD private_person TINYINT(1) NOT NULL');
    }
}
