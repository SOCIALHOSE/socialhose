<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170803125221 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notification_schedule ADD history_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notification_schedule ADD CONSTRAINT FK_F28295E1E058452 FOREIGN KEY (history_id) REFERENCES notifications_history (id)');
        $this->addSql('CREATE INDEX IDX_F28295E1E058452 ON notification_schedule (history_id)');
        $this->addSql('ALTER TABLE internal_notification_scheduling ADD schedules LONGTEXT NOT NULL');
        $this->addSql('UPDATE internal_notification_scheduling SET schedules = \'\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notification_schedule DROP FOREIGN KEY FK_F28295E1E058452');
        $this->addSql('DROP INDEX IDX_F28295E1E058452 ON notification_schedule');
        $this->addSql('ALTER TABLE notification_schedule DROP history_id');
        $this->addSql('ALTER TABLE internal_notification_scheduling DROP schedules');
    }
}
