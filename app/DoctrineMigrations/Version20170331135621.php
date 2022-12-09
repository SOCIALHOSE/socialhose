<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170331135621 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sources_to_sections DROP FOREIGN KEY FK_475C417ED823E37A');
        $this->addSql('ALTER TABLE sources_to_sections DROP FOREIGN KEY FK_475C417E953C1C61');
        $this->addSql('ALTER TABLE sources_to_sources_lists DROP FOREIGN KEY FK_6411C8FE953C1C61');
        $this->addSql('CREATE TABLE cross_sources_source_lists (source VARCHAR(255) NOT NULL, list_id INT NOT NULL, INDEX IDX_5972F68E3DAE168B (list_id), PRIMARY KEY(source, list_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cross_sources_source_lists ADD CONSTRAINT FK_5972F68E3DAE168B FOREIGN KEY (list_id) REFERENCES source_list (id)');
        $this->addSql('DROP TABLE sections');
        $this->addSql('DROP TABLE sources');
        $this->addSql('DROP TABLE sources_to_sections');
        $this->addSql('DROP TABLE sources_to_sources_lists');
        $this->addSql('ALTER TABLE source_list ADD source_number INT NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sections (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sources (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, license VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, country VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, rank INT NOT NULL, url VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, source_publisher_type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, state VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, city VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, lang VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, deleted TINYINT(1) NOT NULL, INDEX source_search_idx (title, type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sources_to_sections (source_id INT NOT NULL, section_id INT NOT NULL, INDEX IDX_475C417E953C1C61 (source_id), INDEX IDX_475C417ED823E37A (section_id), PRIMARY KEY(source_id, section_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sources_to_sources_lists (source_list_id INT NOT NULL, source_id INT NOT NULL, INDEX IDX_6411C8FE953C1C61 (source_id), INDEX IDX_6411C8FE7471AEE3 (source_list_id), PRIMARY KEY(source_list_id, source_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sources_to_sections ADD CONSTRAINT FK_475C417E953C1C61 FOREIGN KEY (source_id) REFERENCES sources (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sources_to_sections ADD CONSTRAINT FK_475C417ED823E37A FOREIGN KEY (section_id) REFERENCES sections (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sources_to_sources_lists ADD CONSTRAINT FK_6411C8FE7471AEE3 FOREIGN KEY (source_list_id) REFERENCES source_list (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sources_to_sources_lists ADD CONSTRAINT FK_6411C8FE953C1C61 FOREIGN KEY (source_id) REFERENCES sources (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE cross_sources_source_lists');
        $this->addSql('ALTER TABLE source_list DROP source_number');
    }
}
