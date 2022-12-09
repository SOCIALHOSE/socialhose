<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170302072240 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE template (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, deleted TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('
            CREATE TABLE charts_templates (
                chart_id INT NOT NULL,
                template_id INT NOT NULL,
                INDEX IDX_3B4D6648BEF83E0A (chart_id),
                INDEX IDX_3B4D66485DA0FB8 (template_id),
                PRIMARY KEY(chart_id, template_id)
            )
             DEFAULT CHARACTER SET utf8
             COLLATE utf8_unicode_ci ENGINE = InnoDB
         ');
        $this->addSql('CREATE TABLE chart_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, deleted TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE charts_templates ADD CONSTRAINT FK_3B4D6648BEF83E0A FOREIGN KEY (chart_id) REFERENCES chart (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE charts_templates ADD CONSTRAINT FK_3B4D66485DA0FB8 FOREIGN KEY (template_id) REFERENCES template (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chart ADD chart_category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE chart ADD CONSTRAINT FK_E5562A2A1E65F97D FOREIGN KEY (chart_category_id) REFERENCES chart_category (id)');
        $this->addSql('CREATE INDEX IDX_E5562A2A1E65F97D ON chart (chart_category_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE charts_templates DROP FOREIGN KEY FK_3B4D66485DA0FB8');
        $this->addSql('ALTER TABLE chart DROP FOREIGN KEY FK_E5562A2A1E65F97D');
        $this->addSql('DROP TABLE template');
        $this->addSql('DROP TABLE charts_templates');
        $this->addSql('DROP TABLE chart_category');
        $this->addSql('DROP INDEX IDX_E5562A2A1E65F97D ON chart');
        $this->addSql('ALTER TABLE chart DROP chart_category_id');
    }
}
