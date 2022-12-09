<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170418123149 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE notifications (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, subject VARCHAR(255) DEFAULT NULL, automated_subject TINYINT(1) NOT NULL, published TINYINT(1) NOT NULL, allow_unsubscribe TINYINT(1) NOT NULL, unsubscribe_notification TINYINT(1) NOT NULL, enhanced_html TINYINT(1) NOT NULL, send_when_empty TINYINT(1) NOT NULL, timezone VARCHAR(255) NOT NULL, send_until DATE DEFAULT NULL, active TINYINT(1) NOT NULL, type VARCHAR(255) NOT NULL, article_extracts VARCHAR(11) DEFAULT NULL, highlight_keywords TINYINT(1) DEFAULT NULL, show_source_country TINYINT(1) DEFAULT NULL, show_user_comments TINYINT(1) DEFAULT NULL, show_paragraph_breaks TINYINT(1) DEFAULT NULL, INDEX IDX_6000B0D37E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE abstract_notification_user (abstract_notification_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_D77AF04D25D4BF91 (abstract_notification_id), INDEX IDX_D77AF04DA76ED395 (user_id), PRIMARY KEY(abstract_notification_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE abstract_notification_abstract_feed (abstract_notification_id INT NOT NULL, abstract_feed_id INT NOT NULL, INDEX IDX_897CC52225D4BF91 (abstract_notification_id), INDEX IDX_897CC522F312DA93 (abstract_feed_id), PRIMARY KEY(abstract_notification_id, abstract_feed_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE abstract_notification_chart (abstract_notification_id INT NOT NULL, chart_id INT NOT NULL, INDEX IDX_E261071525D4BF91 (abstract_notification_id), INDEX IDX_E2610715BEF83E0A (chart_id), PRIMARY KEY(abstract_notification_id, chart_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_schedule (id INT AUTO_INCREMENT NOT NULL, notification_id INT DEFAULT NULL, day VARCHAR(10) NOT NULL, time INT NOT NULL, periodically TINYINT(1) NOT NULL, INDEX IDX_F28295EEF1A9D84 (notification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D37E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE abstract_notification_user ADD CONSTRAINT FK_D77AF04D25D4BF91 FOREIGN KEY (abstract_notification_id) REFERENCES notifications (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE abstract_notification_user ADD CONSTRAINT FK_D77AF04DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE abstract_notification_abstract_feed ADD CONSTRAINT FK_897CC52225D4BF91 FOREIGN KEY (abstract_notification_id) REFERENCES notifications (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE abstract_notification_abstract_feed ADD CONSTRAINT FK_897CC522F312DA93 FOREIGN KEY (abstract_feed_id) REFERENCES feeds (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE abstract_notification_chart ADD CONSTRAINT FK_E261071525D4BF91 FOREIGN KEY (abstract_notification_id) REFERENCES notifications (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE abstract_notification_chart ADD CONSTRAINT FK_E2610715BEF83E0A FOREIGN KEY (chart_id) REFERENCES chart (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification_schedule ADD CONSTRAINT FK_F28295EEF1A9D84 FOREIGN KEY (notification_id) REFERENCES notifications (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE abstract_notification_user DROP FOREIGN KEY FK_D77AF04D25D4BF91');
        $this->addSql('ALTER TABLE abstract_notification_abstract_feed DROP FOREIGN KEY FK_897CC52225D4BF91');
        $this->addSql('ALTER TABLE abstract_notification_chart DROP FOREIGN KEY FK_E261071525D4BF91');
        $this->addSql('ALTER TABLE notification_schedule DROP FOREIGN KEY FK_F28295EEF1A9D84');
        $this->addSql('DROP TABLE notifications');
        $this->addSql('DROP TABLE abstract_notification_user');
        $this->addSql('DROP TABLE abstract_notification_abstract_feed');
        $this->addSql('DROP TABLE abstract_notification_chart');
        $this->addSql('DROP TABLE notification_schedule');
    }
}
