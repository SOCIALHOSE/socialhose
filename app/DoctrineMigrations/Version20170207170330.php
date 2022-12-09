<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170207170330 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE TABLE source_list (
              id INT AUTO_INCREMENT NOT NULL,
              user_id INT DEFAULT NULL,
              title VARCHAR(255) NOT NULL,
              created_at DATETIME NOT NULL,
              updated_at DATETIME NOT NULL,
              deleted TINYINT(1) NOT NULL,
              INDEX IDX_45427D1BA76ED395 (user_id),
              PRIMARY KEY(id)
            )
             DEFAULT CHARACTER SET utf8
             COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('
            CREATE TABLE sources_to_sources_lists (
              source_id INT NOT NULL,
              source_list_id INT NOT NULL,
              INDEX IDX_6411C8FE953C1C61 (source_id),
              INDEX IDX_6411C8FE7471AEE3 (source_list_id),
              PRIMARY KEY(source_id, source_list_id)
            )
             DEFAULT CHARACTER SET utf8
             COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('
            ALTER TABLE source_list
            ADD CONSTRAINT FK_45427D1BA76ED395
            FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('
            ALTER TABLE sources_to_sources_lists
            ADD CONSTRAINT FK_6411C8FE953C1C61
            FOREIGN KEY (source_id) REFERENCES sources (id) ON DELETE CASCADE');
        $this->addSql('
            ALTER TABLE sources_to_sources_lists
            ADD CONSTRAINT FK_6411C8FE7471AEE3
            FOREIGN KEY (source_list_id) REFERENCES source_list (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sources_to_sources_lists DROP FOREIGN KEY FK_6411C8FE7471AEE3');
        $this->addSql('DROP TABLE source_list');
        $this->addSql('DROP TABLE sources_to_sources_lists');
    }
}
