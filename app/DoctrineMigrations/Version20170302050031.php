<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170302050031 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE TABLE saved_analyse (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(255) NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                PRIMARY KEY(id)
            )
             DEFAULT CHARACTER SET utf8
             COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            CREATE TABLE analytic_reports_to_charts (
                analytic_report_id INT NOT NULL,
                chart_id INT NOT NULL,
                INDEX IDX_EEB700806ADC7F69 (analytic_report_id),
                INDEX IDX_EEB70080BEF83E0A (chart_id),
                PRIMARY KEY(analytic_report_id, chart_id)
            )
            DEFAULT CHARACTER SET utf8
            COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            CREATE TABLE analytics_reports_to_queries (
                analytic_report_id INT NOT NULL,
                stored_query_id INT NOT NULL,
                INDEX IDX_1D5B43456ADC7F69 (analytic_report_id),
                INDEX IDX_1D5B4345C113F4B (stored_query_id),
                PRIMARY KEY(analytic_report_id, stored_query_id)
            )
            DEFAULT CHARACTER SET utf8
            COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            CREATE TABLE chart (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(255) NOT NULL,
                identifier VARCHAR(255) NOT NULL,
                deleted TINYINT(1) NOT NULL,
                PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8
            COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            ALTER TABLE analytic_reports_to_charts
            ADD CONSTRAINT FK_EEB700806ADC7F69
            FOREIGN KEY (analytic_report_id) REFERENCES saved_analyse (id)
        ');
        $this->addSql('
            ALTER TABLE analytic_reports_to_charts
            ADD CONSTRAINT FK_EEB70080BEF83E0A
            FOREIGN KEY (chart_id) REFERENCES chart (id)
        ');
        $this->addSql('
            ALTER TABLE analytics_reports_to_queries
            ADD CONSTRAINT FK_1D5B43456ADC7F69
            FOREIGN KEY (analytic_report_id) REFERENCES saved_analyse (id)
        ');
        $this->addSql('
            ALTER TABLE analytics_reports_to_queries
            ADD CONSTRAINT FK_1D5B4345C113F4B
            FOREIGN KEY (stored_query_id) REFERENCES queries (id)
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE analytic_reports_to_charts DROP FOREIGN KEY FK_EEB700806ADC7F69');
        $this->addSql('ALTER TABLE analytics_reports_to_queries DROP FOREIGN KEY FK_1D5B43456ADC7F69');
        $this->addSql('ALTER TABLE analytic_reports_to_charts DROP FOREIGN KEY FK_EEB70080BEF83E0A');
        $this->addSql('DROP TABLE saved_analyse');
        $this->addSql('DROP TABLE analytic_reports_to_charts');
        $this->addSql('DROP TABLE analytics_reports_to_queries');
        $this->addSql('DROP TABLE chart');
    }
}
