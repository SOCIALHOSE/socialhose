<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170207160549 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            ALTER TABLE sources
            ADD user_id INT DEFAULT NULL,
            ADD source_publisher_type VARCHAR(255) NOT NULL,
            ADD state VARCHAR(255) NOT NULL,
            ADD city VARCHAR(255) NOT NULL,
            ADD lang VARCHAR(255) NOT NULL,
            ADD section VARCHAR(255) NOT NULL,
            ADD deleted TINYINT(1) NOT NULL,
            CHANGE media_type type VARCHAR(255) NOT NULL
        ');
        $this->addSql('
            ALTER TABLE sources
            ADD CONSTRAINT FK_D25D65F2A76ED395 FOREIGN KEY (user_id)
            REFERENCES users (id) ON DELETE CASCADE
        ');
        $this->addSql('CREATE INDEX IDX_D25D65F2A76ED395 ON sources (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sources DROP FOREIGN KEY FK_D25D65F2A76ED395');
        $this->addSql('DROP INDEX IDX_D25D65F2A76ED395 ON sources');
        $this->addSql('
            ALTER TABLE sources
            ADD media_type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci,
            DROP user_id,
            DROP type,
            DROP source_publisher_type,
            DROP state,
            DROP city,
            DROP lang,
            DROP section,
            DROP deleted
        ');
    }
}
