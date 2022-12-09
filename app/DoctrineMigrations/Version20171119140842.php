<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171119140842 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE analytic_reports_to_charts DROP FOREIGN KEY FK_EEB70080BEF83E0A');
        $this->addSql('ALTER TABLE charts_to_chart_templates DROP FOREIGN KEY FK_8A840E3BBEF83E0A');
        $this->addSql('ALTER TABLE cross_notifications_charts DROP FOREIGN KEY FK_325EA2F0BEF83E0A');
        $this->addSql('ALTER TABLE chart DROP FOREIGN KEY FK_E5562A2A1E65F97D');
        $this->addSql('ALTER TABLE charts_to_chart_templates DROP FOREIGN KEY FK_8A840E3B5DA0FB8');
        $this->addSql('ALTER TABLE analytic_reports_to_charts DROP FOREIGN KEY FK_EEB700806ADC7F69');
        $this->addSql('ALTER TABLE analytics_reports_to_feeds DROP FOREIGN KEY FK_3613776B6ADC7F69');
        $this->addSql('CREATE TABLE cache_items (`key` VARCHAR(255) NOT NULL, value LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', lifetime INT NOT NULL, expires_at BIGINT NOT NULL, PRIMARY KEY(`key`)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE analytic_reports_to_charts');
        $this->addSql('DROP TABLE analytics_reports_to_feeds');
        $this->addSql('DROP TABLE chart');
        $this->addSql('DROP TABLE chart_category');
        $this->addSql('DROP TABLE chart_template');
        $this->addSql('DROP TABLE charts_to_chart_templates');
        $this->addSql('DROP TABLE cross_notifications_charts');
        $this->addSql('DROP TABLE filters_values');
        $this->addSql('DROP TABLE saved_analyse');
        $this->addSql('ALTER TABLE documents DROP html, DROP html_length');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE analytic_reports_to_charts (analytic_report_id INT NOT NULL, chart_id INT NOT NULL, INDEX IDX_EEB700806ADC7F69 (analytic_report_id), INDEX IDX_EEB70080BEF83E0A (chart_id), PRIMARY KEY(analytic_report_id, chart_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE analytics_reports_to_feeds (analytic_report_id INT NOT NULL, feed_id INT NOT NULL, INDEX IDX_3613776B6ADC7F69 (analytic_report_id), INDEX IDX_3613776B51A5BC03 (feed_id), PRIMARY KEY(analytic_report_id, feed_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chart (id INT AUTO_INCREMENT NOT NULL, chart_category_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, identifier VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, deleted TINYINT(1) NOT NULL, INDEX IDX_E5562A2A1E65F97D (chart_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chart_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, deleted TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chart_template (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, deleted TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE charts_to_chart_templates (chart_id INT NOT NULL, chart_template_id INT NOT NULL, INDEX IDX_8A840E3BBEF83E0A (chart_id), INDEX IDX_8A840E3B9F96B27 (chart_template_id), PRIMARY KEY(chart_id, chart_template_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cross_notifications_charts (notification_id INT NOT NULL, chart_id INT NOT NULL, INDEX IDX_325EA2F0EF1A9D84 (notification_id), INDEX IDX_325EA2F0BEF83E0A (chart_id), PRIMARY KEY(notification_id, chart_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE filters_values (hash VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, data LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\', expires_at DATETIME NOT NULL, PRIMARY KEY(hash)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE saved_analyse (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_BF1AC3E9A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE analytic_reports_to_charts ADD CONSTRAINT FK_EEB700806ADC7F69 FOREIGN KEY (analytic_report_id) REFERENCES saved_analyse (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE analytic_reports_to_charts ADD CONSTRAINT FK_EEB70080BEF83E0A FOREIGN KEY (chart_id) REFERENCES chart (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE analytics_reports_to_feeds ADD CONSTRAINT FK_3613776B51A5BC03 FOREIGN KEY (feed_id) REFERENCES feeds (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE analytics_reports_to_feeds ADD CONSTRAINT FK_3613776B6ADC7F69 FOREIGN KEY (analytic_report_id) REFERENCES saved_analyse (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chart ADD CONSTRAINT FK_E5562A2A1E65F97D FOREIGN KEY (chart_category_id) REFERENCES chart_category (id)');
        $this->addSql('ALTER TABLE charts_to_chart_templates ADD CONSTRAINT FK_8A840E3B5DA0FB8 FOREIGN KEY (chart_template_id) REFERENCES chart_template (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE charts_to_chart_templates ADD CONSTRAINT FK_8A840E3BBEF83E0A FOREIGN KEY (chart_id) REFERENCES chart (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cross_notifications_charts ADD CONSTRAINT FK_325EA2F0BEF83E0A FOREIGN KEY (chart_id) REFERENCES chart (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cross_notifications_charts ADD CONSTRAINT FK_325EA2F0EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notifications (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE saved_analyse ADD CONSTRAINT FK_BF1AC3E9A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('DROP TABLE cache_items');
        $this->addSql('ALTER TABLE documents ADD html LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, ADD html_length INT DEFAULT NULL');
    }
}
