<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170323122000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE documents ADD source_date_found DATETIME DEFAULT NULL, DROP source_hashcode, DROP source_content_length, DROP source_content_checksum, DROP main_checksum, DROP main_format, DROP description, DROP type');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE documents ADD source_hashcode VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD source_content_length INT DEFAULT NULL, ADD source_content_checksum VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD main_checksum VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD main_format VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD description VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD type VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP source_date_found');
    }
}
