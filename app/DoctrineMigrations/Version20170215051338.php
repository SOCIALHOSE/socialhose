<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170215051338 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sources_to_sections (source_id INT NOT NULL, section_id INT NOT NULL, INDEX IDX_475C417E953C1C61 (source_id), INDEX IDX_475C417ED823E37A (section_id), PRIMARY KEY(source_id, section_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sections (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sources_to_sections ADD CONSTRAINT FK_475C417E953C1C61 FOREIGN KEY (source_id) REFERENCES sources (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sources_to_sections ADD CONSTRAINT FK_475C417ED823E37A FOREIGN KEY (section_id) REFERENCES sections (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sources_to_sections DROP FOREIGN KEY FK_475C417ED823E37A');
        $this->addSql('DROP TABLE sources_to_sections');
        $this->addSql('DROP TABLE sections');
    }
}
