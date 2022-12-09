<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170830113930 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE billing_subscription_agreement (id INT AUTO_INCREMENT NOT NULL, subscription_id INT DEFAULT NULL, agreement_id VARCHAR(255) NOT NULL, INDEX IDX_9BD6D8479A1887DC (subscription_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE billing_subscription_agreement ADD CONSTRAINT FK_9BD6D8479A1887DC FOREIGN KEY (subscription_id) REFERENCES subscriptions (id)');
        $this->addSql('DROP TABLE payment_tokens');
        $this->addSql('ALTER TABLE subscriptions ADD payed TINYINT(1) NOT NULL');
        $this->addSql('UPDATE subscriptions SET payed = false');
        $this->addSql('ALTER TABLE payments ADD gateway VARCHAR(255) NOT NULL COMMENT \'(DC2Type:payment_gateway)\', ADD created_at DATETIME NOT NULL, ADD success TINYINT(1) NOT NULL, ADD amount_amount NUMERIC(10, 2) NOT NULL, ADD amount_currency VARCHAR(4) NOT NULL, DROP number, DROP description, DROP client_email, DROP client_id, DROP currency_code, DROP details, CHANGE total_amount subscription_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B329A1887DC FOREIGN KEY (subscription_id) REFERENCES subscriptions (id)');
        $this->addSql('CREATE INDEX IDX_65D29B329A1887DC ON payments (subscription_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE payment_tokens (hash VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, details LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:object)\', after_url LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, target_url LONGTEXT NOT NULL COLLATE utf8_unicode_ci, gateway_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(hash)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE billing_subscription_agreement');
        $this->addSql('ALTER TABLE payments DROP FOREIGN KEY FK_65D29B329A1887DC');
        $this->addSql('DROP INDEX IDX_65D29B329A1887DC ON payments');
        $this->addSql('ALTER TABLE payments ADD number VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD description VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD client_email VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD client_id VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD currency_code VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD details LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\', DROP gateway, DROP created_at, DROP success, DROP amount_amount, DROP amount_currency, CHANGE subscription_id total_amount INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subscriptions DROP payed');
    }
}
