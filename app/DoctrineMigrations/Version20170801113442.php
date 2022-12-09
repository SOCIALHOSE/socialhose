<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170801113442 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE subscriptions (id INT AUTO_INCREMENT NOT NULL, plan_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, user_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, organization_address VARCHAR(255) DEFAULT NULL, organization_email VARCHAR(255) DEFAULT NULL, organization_phone VARCHAR(255) DEFAULT NULL, INDEX IDX_4778A01E899029B (plan_id), INDEX IDX_4778A0132C8A3DE (organization_id), INDEX IDX_4778A01A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subscriptions ADD CONSTRAINT FK_4778A01E899029B FOREIGN KEY (plan_id) REFERENCES plan (id)');
        $this->addSql('ALTER TABLE subscriptions ADD CONSTRAINT FK_4778A0132C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');
        $this->addSql('ALTER TABLE subscriptions ADD CONSTRAINT FK_4778A01A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('DROP TABLE subscription');
        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, user_id INT DEFAULT NULL, plan_id INT DEFAULT NULL, organization_address VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, organization_email VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, organization_phone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, INDEX IDX_A3C664D3E899029B (plan_id), INDEX IDX_A3C664D332C8A3DE (organization_id), INDEX IDX_A3C664D3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D332C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3E899029B FOREIGN KEY (plan_id) REFERENCES plan (id)');
        $this->addSql('DROP TABLE subscriptions');
        $this->addSql('ALTER TABLE notifications CHANGE notification_type notification_type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE theme_type theme_type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE timezone timezone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}
