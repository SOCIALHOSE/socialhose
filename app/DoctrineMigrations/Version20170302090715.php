<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170302090715 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE charts_templates DROP FOREIGN KEY FK_3B4D66485DA0FB8');
        $this->addSql('CREATE TABLE chart_template (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, deleted TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('
            CREATE TABLE charts_to_chart_templates (
                chart_id INT NOT NULL,
                chart_template_id INT NOT NULL,
                INDEX IDX_8A840E3BBEF83E0A (chart_id),
                INDEX IDX_8A840E3B5DA0FB8 (chart_template_id),
                PRIMARY KEY(chart_id, chart_template_id)
            )
             DEFAULT CHARACTER SET utf8
             COLLATE utf8_unicode_ci ENGINE = InnoDB
         ');
        $this->addSql('ALTER TABLE charts_to_chart_templates ADD CONSTRAINT FK_8A840E3BBEF83E0A FOREIGN KEY (chart_id) REFERENCES chart (id) ON DELETE CASCADE');
        $this->addSql('
            ALTER TABLE charts_to_chart_templates
            ADD CONSTRAINT FK_8A840E3B5DA0FB8
            FOREIGN KEY (chart_template_id)
            REFERENCES chart_template (id) ON DELETE CASCADE
        ');
        $this->addSql('DROP TABLE charts_templates');
        $this->addSql('DROP TABLE template');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE charts_to_chart_templates DROP FOREIGN KEY FK_8A840E3B5DA0FB8');
        $this->addSql('CREATE TABLE charts_templates (chart_id INT NOT NULL, template_id INT NOT NULL, INDEX IDX_3B4D6648BEF83E0A (chart_id), INDEX IDX_3B4D66485DA0FB8 (template_id), PRIMARY KEY(chart_id, template_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE template (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, deleted TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE charts_templates ADD CONSTRAINT FK_3B4D66485DA0FB8 FOREIGN KEY (template_id) REFERENCES template (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE charts_templates ADD CONSTRAINT FK_3B4D6648BEF83E0A FOREIGN KEY (chart_id) REFERENCES chart (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE chart_template');
        $this->addSql('DROP TABLE charts_to_chart_templates');
    }
}
