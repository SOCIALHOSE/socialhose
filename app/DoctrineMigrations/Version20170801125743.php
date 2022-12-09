<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170801125743 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE source_list DROP number_sources, CHANGE source_number source_number INT NOT NULL');
        $this->addSql('ALTER TABLE plan ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE notifications ADD billing_subscription_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3CF9564CB FOREIGN KEY (billing_subscription_id) REFERENCES subscriptions (id)');
        $this->addSql('CREATE INDEX IDX_6000B0D3CF9564CB ON notifications (billing_subscription_id)');
        $this->addSql('DROP INDEX UNIQ_427C1C7F5E237E06 ON organizations');
        $this->addSql('ALTER TABLE organizations CHANGE organization_name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE users ADD billing_subscription_id INT DEFAULT NULL, DROP organization');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9CF9564CB FOREIGN KEY (billing_subscription_id) REFERENCES subscriptions (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E9CF9564CB ON users (billing_subscription_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3CF9564CB');
        $this->addSql('DROP INDEX IDX_6000B0D3CF9564CB ON notifications');
        $this->addSql('ALTER TABLE notifications DROP billing_subscription_id');
        $this->addSql('ALTER TABLE organizations CHANGE name organization_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_427C1C7F5E237E06 ON organizations (organization_name)');
        $this->addSql('ALTER TABLE plan DROP name');
        $this->addSql('ALTER TABLE source_list ADD number_sources INT DEFAULT NULL, CHANGE source_number source_number INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9CF9564CB');
        $this->addSql('DROP INDEX IDX_1483A5E9CF9564CB ON users');
        $this->addSql('ALTER TABLE users ADD organization VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP billing_subscription_id');
    }
}
