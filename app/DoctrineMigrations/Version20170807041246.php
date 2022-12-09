<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170807041246 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE plan ADD searches_per_day INT NOT NULL, ADD saved_feeds INT NOT NULL, ADD master_accounts INT NOT NULL, ADD subscriber_accounts INT NOT NULL, ADD alerts INT NOT NULL, ADD newsletters INT NOT NULL, ADD analytics TINYINT(1) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notifications CHANGE notification_type notification_type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE theme_type theme_type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE timezone timezone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE plan DROP searches_per_day, DROP saved_feeds, DROP master_accounts, DROP subscriber_accounts, DROP alerts, DROP newsletters, DROP analytics');
    }
}
