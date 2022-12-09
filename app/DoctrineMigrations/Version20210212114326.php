<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20210212114326 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        /*
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE plan ADD news TINYINT(1) NOT NULL, ADD blog TINYINT(1) NOT NULL, ADD reddit TINYINT(1) NOT NULL, ADD instagram TINYINT(1) NOT NULL, ADD twitter TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE subscriptions DROP searchLicence, DROP webFeedLicence, DROP newsletterLicences, DROP useAccounts, DROP newsletter, DROP news, DROP blog, DROP reddit, DROP instagram, DROP twitter, DROP analytics');
        */
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE plan DROP news, DROP blog, DROP reddit, DROP instagram, DROP twitter');
        $this->addSql('ALTER TABLE subscriptions ADD searchLicence INT NOT NULL, ADD webFeedLicence INT NOT NULL, ADD newsletterLicences INT NOT NULL, ADD useAccounts INT NOT NULL, ADD newsletter INT NOT NULL, ADD news TINYINT(1) NOT NULL, ADD blog TINYINT(1) NOT NULL, ADD reddit TINYINT(1) NOT NULL, ADD instagram TINYINT(1) NOT NULL, ADD twitter TINYINT(1) NOT NULL, ADD analytics TINYINT(1) NOT NULL');
    }
}
