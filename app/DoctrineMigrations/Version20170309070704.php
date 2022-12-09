<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170309070704 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE analytics_reports_to_feeds (analytic_report_id INT NOT NULL, feed_id INT NOT NULL, INDEX IDX_3613776B6ADC7F69 (analytic_report_id), INDEX IDX_3613776B51A5BC03 (feed_id), PRIMARY KEY(analytic_report_id, feed_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE analytics_reports_to_feeds ADD CONSTRAINT FK_3613776B6ADC7F69 FOREIGN KEY (analytic_report_id) REFERENCES saved_analyse (id)');
        $this->addSql('ALTER TABLE analytics_reports_to_feeds ADD CONSTRAINT FK_3613776B51A5BC03 FOREIGN KEY (feed_id) REFERENCES feeds (id)');
        $this->addSql('DROP TABLE analytics_reports_to_queries');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE analytics_reports_to_queries (analytic_report_id INT NOT NULL, stored_query_id INT NOT NULL, INDEX IDX_1D5B43456ADC7F69 (analytic_report_id), INDEX IDX_1D5B4345C113F4B (stored_query_id), PRIMARY KEY(analytic_report_id, stored_query_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE analytics_reports_to_queries ADD CONSTRAINT FK_1D5B43456ADC7F69 FOREIGN KEY (analytic_report_id) REFERENCES saved_analyse (id)');
        $this->addSql('ALTER TABLE analytics_reports_to_queries ADD CONSTRAINT FK_1D5B4345C113F4B FOREIGN KEY (stored_query_id) REFERENCES queries (id)');
        $this->addSql('DROP TABLE analytics_reports_to_feeds');
    }
}
