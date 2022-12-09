<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170309093604 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE analytic_reports_to_charts DROP FOREIGN KEY FK_EEB700806ADC7F69');
        $this->addSql('ALTER TABLE analytic_reports_to_charts DROP FOREIGN KEY FK_EEB70080BEF83E0A');
        $this->addSql('ALTER TABLE analytic_reports_to_charts ADD CONSTRAINT FK_EEB700806ADC7F69 FOREIGN KEY (analytic_report_id) REFERENCES saved_analyse (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE analytic_reports_to_charts ADD CONSTRAINT FK_EEB70080BEF83E0A FOREIGN KEY (chart_id) REFERENCES chart (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE analytics_reports_to_feeds DROP FOREIGN KEY FK_3613776B51A5BC03');
        $this->addSql('ALTER TABLE analytics_reports_to_feeds DROP FOREIGN KEY FK_3613776B6ADC7F69');
        $this->addSql('ALTER TABLE analytics_reports_to_feeds ADD CONSTRAINT FK_3613776B51A5BC03 FOREIGN KEY (feed_id) REFERENCES feeds (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE analytics_reports_to_feeds ADD CONSTRAINT FK_3613776B6ADC7F69 FOREIGN KEY (analytic_report_id) REFERENCES saved_analyse (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE analytic_reports_to_charts DROP FOREIGN KEY FK_EEB700806ADC7F69');
        $this->addSql('ALTER TABLE analytic_reports_to_charts DROP FOREIGN KEY FK_EEB70080BEF83E0A');
        $this->addSql('ALTER TABLE analytic_reports_to_charts ADD CONSTRAINT FK_EEB700806ADC7F69 FOREIGN KEY (analytic_report_id) REFERENCES saved_analyse (id)');
        $this->addSql('ALTER TABLE analytic_reports_to_charts ADD CONSTRAINT FK_EEB70080BEF83E0A FOREIGN KEY (chart_id) REFERENCES chart (id)');
        $this->addSql('ALTER TABLE analytics_reports_to_feeds DROP FOREIGN KEY FK_3613776B6ADC7F69');
        $this->addSql('ALTER TABLE analytics_reports_to_feeds DROP FOREIGN KEY FK_3613776B51A5BC03');
        $this->addSql('ALTER TABLE analytics_reports_to_feeds ADD CONSTRAINT FK_3613776B6ADC7F69 FOREIGN KEY (analytic_report_id) REFERENCES saved_analyse (id)');
        $this->addSql('ALTER TABLE analytics_reports_to_feeds ADD CONSTRAINT FK_3613776B51A5BC03 FOREIGN KEY (feed_id) REFERENCES feeds (id)');
    }
}
