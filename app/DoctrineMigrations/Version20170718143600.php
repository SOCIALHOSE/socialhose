<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170718143600 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE cross_notifications_feeds (notification_id INT NOT NULL, abstract_feed_id INT NOT NULL, INDEX IDX_36DBBC7CEF1A9D84 (notification_id), INDEX IDX_36DBBC7CF312DA93 (abstract_feed_id), PRIMARY KEY(notification_id, abstract_feed_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cross_notifications_charts (notification_id INT NOT NULL, chart_id INT NOT NULL, INDEX IDX_325EA2F0EF1A9D84 (notification_id), INDEX IDX_325EA2F0BEF83E0A (chart_id), PRIMARY KEY(notification_id, chart_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_themes (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, `default` TINYINT(1) NOT NULL, enhanced_summary LONGTEXT NOT NULL, enhanced_conclusion LONGTEXT NOT NULL, enhanced_header LONGTEXT NOT NULL COMMENT \'(DC2Type:object)\', enhanced_fonts LONGTEXT NOT NULL COMMENT \'(DC2Type:object)\', enhanced_content LONGTEXT NOT NULL COMMENT \'(DC2Type:object)\', enhanced_colors LONGTEXT NOT NULL COMMENT \'(DC2Type:object)\', plain_summary LONGTEXT NOT NULL, plain_conclusion LONGTEXT NOT NULL, plain_header LONGTEXT NOT NULL COMMENT \'(DC2Type:object)\', plain_fonts LONGTEXT NOT NULL COMMENT \'(DC2Type:object)\', plain_content LONGTEXT NOT NULL COMMENT \'(DC2Type:object)\', plain_colors LONGTEXT NOT NULL COMMENT \'(DC2Type:object)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipients (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, subscribed_count LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, active TINYINT(1) NOT NULL, type VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, persons_count INT DEFAULT NULL, INDEX IDX_146632C47E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cross_recipient_notifications (abstract_recipient_id INT NOT NULL, notification_id INT NOT NULL, INDEX IDX_CAECF31E7A443649 (abstract_recipient_id), INDEX IDX_CAECF31EEF1A9D84 (notification_id), PRIMARY KEY(abstract_recipient_id, notification_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cross_groups_persons (group_recipient_id INT NOT NULL, person_recipient_id INT NOT NULL, INDEX IDX_E37D3AB7569C541 (group_recipient_id), INDEX IDX_E37D3AB7A216F35 (person_recipient_id), PRIMARY KEY(group_recipient_id, person_recipient_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cross_notifications_feeds ADD CONSTRAINT FK_36DBBC7CEF1A9D84 FOREIGN KEY (notification_id) REFERENCES notifications (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cross_notifications_feeds ADD CONSTRAINT FK_36DBBC7CF312DA93 FOREIGN KEY (abstract_feed_id) REFERENCES feeds (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cross_notifications_charts ADD CONSTRAINT FK_325EA2F0EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notifications (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cross_notifications_charts ADD CONSTRAINT FK_325EA2F0BEF83E0A FOREIGN KEY (chart_id) REFERENCES chart (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recipients ADD CONSTRAINT FK_146632C47E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cross_recipient_notifications ADD CONSTRAINT FK_CAECF31E7A443649 FOREIGN KEY (abstract_recipient_id) REFERENCES recipients (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cross_recipient_notifications ADD CONSTRAINT FK_CAECF31EEF1A9D84 FOREIGN KEY (notification_id) REFERENCES notifications (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cross_groups_persons ADD CONSTRAINT FK_E37D3AB7569C541 FOREIGN KEY (group_recipient_id) REFERENCES recipients (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cross_groups_persons ADD CONSTRAINT FK_E37D3AB7A216F35 FOREIGN KEY (person_recipient_id) REFERENCES recipients (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE abstract_notification_abstract_feed');
        $this->addSql('DROP TABLE abstract_notification_chart');
        $this->addSql('DROP TABLE abstract_notification_user');
        $this->addSql('ALTER TABLE notifications ADD theme_id INT DEFAULT NULL, ADD notification_type VARCHAR(255) NOT NULL, ADD theme_type VARCHAR(255) NOT NULL, ADD enhanced_theme_options_diff LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', ADD plain_theme_options_diff LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', DROP enhanced_html, DROP type, DROP article_extracts, DROP highlight_keywords, DROP show_source_country, DROP show_user_comments, DROP show_paragraph_breaks, CHANGE timezone timezone VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D359027487 FOREIGN KEY (theme_id) REFERENCES notification_themes (id)');
        $this->addSql('CREATE INDEX IDX_6000B0D359027487 ON notifications (theme_id)');
        $this->addSql('ALTER TABLE users ADD recipient_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9E92F8F78 FOREIGN KEY (recipient_id) REFERENCES recipients (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E92F8F78 ON users (recipient_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D359027487');
        $this->addSql('ALTER TABLE cross_recipient_notifications DROP FOREIGN KEY FK_CAECF31E7A443649');
        $this->addSql('ALTER TABLE cross_groups_persons DROP FOREIGN KEY FK_E37D3AB7569C541');
        $this->addSql('ALTER TABLE cross_groups_persons DROP FOREIGN KEY FK_E37D3AB7A216F35');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9E92F8F78');
        $this->addSql('CREATE TABLE abstract_notification_abstract_feed (abstract_notification_id INT NOT NULL, abstract_feed_id INT NOT NULL, INDEX IDX_897CC52225D4BF91 (abstract_notification_id), INDEX IDX_897CC522F312DA93 (abstract_feed_id), PRIMARY KEY(abstract_notification_id, abstract_feed_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE abstract_notification_chart (abstract_notification_id INT NOT NULL, chart_id INT NOT NULL, INDEX IDX_E261071525D4BF91 (abstract_notification_id), INDEX IDX_E2610715BEF83E0A (chart_id), PRIMARY KEY(abstract_notification_id, chart_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE abstract_notification_user (abstract_notification_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_D77AF04D25D4BF91 (abstract_notification_id), INDEX IDX_D77AF04DA76ED395 (user_id), PRIMARY KEY(abstract_notification_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE abstract_notification_abstract_feed ADD CONSTRAINT FK_897CC52225D4BF91 FOREIGN KEY (abstract_notification_id) REFERENCES notifications (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE abstract_notification_abstract_feed ADD CONSTRAINT FK_897CC522F312DA93 FOREIGN KEY (abstract_feed_id) REFERENCES feeds (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE abstract_notification_chart ADD CONSTRAINT FK_E261071525D4BF91 FOREIGN KEY (abstract_notification_id) REFERENCES notifications (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE abstract_notification_chart ADD CONSTRAINT FK_E2610715BEF83E0A FOREIGN KEY (chart_id) REFERENCES chart (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE abstract_notification_user ADD CONSTRAINT FK_D77AF04D25D4BF91 FOREIGN KEY (abstract_notification_id) REFERENCES notifications (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE abstract_notification_user ADD CONSTRAINT FK_D77AF04DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE cross_notifications_feeds');
        $this->addSql('DROP TABLE cross_notifications_charts');
        $this->addSql('DROP TABLE notification_themes');
        $this->addSql('DROP TABLE recipients');
        $this->addSql('DROP TABLE cross_recipient_notifications');
        $this->addSql('DROP TABLE cross_groups_persons');
        $this->addSql('DROP INDEX IDX_6000B0D359027487 ON notifications');
        $this->addSql('ALTER TABLE notifications ADD enhanced_html TINYINT(1) NOT NULL, ADD type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD article_extracts VARCHAR(11) DEFAULT NULL COLLATE utf8_unicode_ci, ADD highlight_keywords TINYINT(1) DEFAULT NULL, ADD show_source_country TINYINT(1) DEFAULT NULL, ADD show_user_comments TINYINT(1) DEFAULT NULL, ADD show_paragraph_breaks TINYINT(1) DEFAULT NULL, DROP theme_id, DROP notification_type, DROP theme_type, DROP enhanced_theme_options_diff, DROP plain_theme_options_diff, CHANGE timezone timezone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('DROP INDEX UNIQ_1483A5E9E92F8F78 ON users');
        $this->addSql('ALTER TABLE users DROP recipient_id');
    }
}
