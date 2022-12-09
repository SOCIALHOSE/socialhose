<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170621133416 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE notifications_history (id INT AUTO_INCREMENT NOT NULL, notification_id INT DEFAULT NULL, date DATETIME NOT NULL, INDEX IDX_6227D824EF1A9D84 (notification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notifications_history ADD CONSTRAINT FK_6227D824EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notifications (id)');
        $this->addSql('ALTER TABLE site_settings ADD section VARCHAR(255) NOT NULL, CHANGE value value LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE notifications ADD created_at DATETIME NOT NULL, ADD last_sent_at DATETIME NOT NULL, CHANGE timezone timezone VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE notification_schedule ADD type VARCHAR(255) NOT NULL, ADD days VARCHAR(9) DEFAULT NULL, ADD period VARCHAR(7) DEFAULT NULL, ADD hour SMALLINT DEFAULT NULL, ADD minute SMALLINT DEFAULT NULL, DROP periodically, CHANGE day day VARCHAR(10) DEFAULT NULL, CHANGE time time VARCHAR(5) DEFAULT NULL');

        //
        // Create table for string scheduling. This table don't have ORM mapping.
        //
        $table = $schema->createTable('internal_notification_scheduling');
        $table->addColumn('notification_id', 'integer');
        $table->addColumn('date', 'datetime');
        $table->addForeignKeyConstraint('notifications', [ 'notification_id' ], [ 'id' ]);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE notifications_history');
        $this->addSql('DROP TABLE internal_notification_scheduling');
        $this->addSql('ALTER TABLE notification_schedule ADD periodically TINYINT(1) NOT NULL, DROP type, DROP days, DROP period, DROP hour, DROP minute, CHANGE time time INT NOT NULL, CHANGE day day VARCHAR(10) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE notifications DROP created_at, DROP last_sent_at, CHANGE timezone timezone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE site_settings DROP section, CHANGE value value VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}
