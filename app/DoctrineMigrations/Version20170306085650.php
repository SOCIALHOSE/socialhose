<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170306085650 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE INDEX IDX_8A840E3B9F96B27 ON charts_to_chart_templates (chart_template_id)
        ');
        $this->addSql('
            DROP INDEX idx_8a840e3b5da0fb8 ON charts_to_chart_templates
        ');

//        $this->addSql('ALTER TABLE charts_to_chart_templates RENAME INDEX idx_8a840e3b5da0fb8 TO IDX_8A840E3B9F96B27');
        $this->addSql('ALTER TABLE users CHANGE number_of_subscriber number_of_subscribers INT NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE INDEX IDX_8A840E3B5DA0FB8 ON charts_to_chart_templates (chart_template_id)
        ');
        $this->addSql('
            DROP INDEX idx_8a840e3b9f96b27 ON charts_to_chart_templates
        ');

//        $this->addSql('ALTER TABLE charts_to_chart_templates RENAME INDEX idx_8a840e3b9f96b27 TO IDX_8A840E3B5DA0FB8');
        $this->addSql('ALTER TABLE users CHANGE number_of_subscribers number_of_subscriber INT NOT NULL');
    }
}
