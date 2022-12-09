<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170904125133 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE subscriptions ADD gateway VARCHAR(255) NOT NULL COMMENT \'(DC2Type:payment_gateway)\'');
        $this->addSql('ALTER TABLE billing_subscription_agreement ADD gateway VARCHAR(255) NOT NULL COMMENT \'(DC2Type:payment_gateway)\'');
        $this->addSql('ALTER TABLE payments ADD transaction_id VARCHAR(255) NOT NULL, ADD status VARCHAR(255) NOT NULL COMMENT \'(DC2Type:payment_status)\', DROP success');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE billing_subscription_agreement DROP gateway');
        $this->addSql('ALTER TABLE payments ADD success TINYINT(1) NOT NULL, DROP transaction_id, DROP status');
        $this->addSql('ALTER TABLE subscriptions DROP gateway');
    }
}
