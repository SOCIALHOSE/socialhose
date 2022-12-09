<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161222151030 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            ALTER TABLE documents
            CHANGE source_assigned_tags source_assigned_tags LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\',
            CHANGE links links LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            ALTER TABLE documents
            CHANGE source_assigned_tags source_assigned_tags VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci,
            CHANGE links links VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci
        ');
    }
}
