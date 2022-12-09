<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170306121643 extends AbstractMigration
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
            ADD point VARCHAR(255) DEFAULT NULL,
            ADD duplicates_count INT DEFAULT NULL,
            DROP sequence_range,
            DROP hashcode,
            DROP index_method,
            DROP detection_method,
            DROP version,
            DROP source_resource,
            DROP source_last_updated,
            DROP source_update_interval,
            DROP source_http_status,
            DROP strategy_source_setting_update,
            DROP strategy_source_setting_index,
            DROP source_feed_href,
            DROP source_feed_title,
            DROP source_feed_format,
            DROP permalink_redirect,
            DROP permalink_redirect_domain,
            DROP permalink_redirect_site,
            DROP canonical,
            DROP domain,
            DROP site,
            DROP extract,
            DROP extract_length,
            DROP extract_checksum,
            DROP card,
            DROP metadata_score,
            CHANGE section section LONGTEXT DEFAULT NULL,
            CHANGE source_assigned_tags tags LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE documents ADD sequence_range BIGINT DEFAULT NULL, ADD hashcode LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, ADD detection_method VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD version VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD source_resource VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD source_last_updated DATETIME DEFAULT NULL, ADD source_update_interval BIGINT DEFAULT NULL, ADD strategy_source_setting_update VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD strategy_source_setting_index VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD source_feed_href VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD source_feed_title VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD source_feed_format VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD permalink_redirect VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD permalink_redirect_domain VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD permalink_redirect_site VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD canonical VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD domain VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD site VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD extract LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, ADD extract_length INT DEFAULT NULL, ADD extract_checksum VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD card VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD metadata_score INT DEFAULT NULL, CHANGE section section VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE point index_method VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE duplicates_count source_http_status INT DEFAULT NULL, CHANGE tags source_assigned_tags LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\'');
    }
}
