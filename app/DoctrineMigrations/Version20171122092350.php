<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171122092350 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //
        // Drop foreign keys to `documents` table.
        //
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AC33F7837');
        $this->addSql('ALTER TABLE comments CHANGE document_id document_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE pages DROP FOREIGN KEY FK_2074E575C33F7837');
        $this->addSql('ALTER TABLE pages CHANGE document_id document_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE deleted_documents DROP FOREIGN KEY FK_A3B88FFDDD472672');
        $this->addSql('DROP INDEX IDX_A3B88FFDDD472672 ON deleted_documents');
        $this->addSql('ALTER TABLE deleted_documents ADD document_id VARCHAR(255) NOT NULL, DROP document_sequence');

        //
        // Change `documents` table.
        //
        $this->addSql('ALTER TABLE documents DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE documents ADD id VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE documents SET id = sequence');
        $this->addSql('ALTER TABLE documents ADD platform VARCHAR(255) NOT NULL, ADD data LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', DROP sequence, DROP section, DROP date_found, DROP source_link, DROP source_publisher_type, DROP tags, DROP source_title, DROP source_description, DROP permalink, DROP main, DROP main_length, DROP summary_text, DROP title, DROP published, DROP publisher, DROP links, DROP author_name, DROP author_link, DROP author_gender, DROP image_src, DROP sentiment, DROP lang, DROP country, DROP state, DROP city, DROP shares, DROP views, DROP source_location, DROP shared_identifier, DROP point, DROP duplicates_count, DROP source_publisher_subtype, DROP source_date_found, DROP source_hashcode');
        $this->addSql('ALTER TABLE documents ADD PRIMARY KEY (id)');

        //
        // Create new foreign keys.
        //
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AC33F7837 FOREIGN KEY (document_id) REFERENCES documents (id)');
        $this->addSql('ALTER TABLE pages ADD CONSTRAINT FK_2074E575C33F7837 FOREIGN KEY (document_id) REFERENCES documents (id)');
        $this->addSql('ALTER TABLE deleted_documents DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE deleted_documents ADD CONSTRAINT FK_A3B88FFDC33F7837 FOREIGN KEY (document_id) REFERENCES documents (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_A3B88FFDC33F7837 ON deleted_documents (document_id)');
        $this->addSql('ALTER TABLE deleted_documents ADD PRIMARY KEY (abstract_feed_id, document_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //
        // Drop foreign keys to `documents` table.
        //
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AC33F7837');
        $this->addSql('ALTER TABLE comments CHANGE document_id document_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE pages DROP FOREIGN KEY FK_2074E575C33F7837');
        $this->addSql('ALTER TABLE pages CHANGE document_id document_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE deleted_documents DROP FOREIGN KEY FK_A3B88FFDC33F7837');
        $this->addSql('DROP INDEX IDX_A3B88FFDC33F7837 ON deleted_documents');
        $this->addSql('ALTER TABLE deleted_documents DROP PRIMARY KEY');

        //
        // Change `documents` table.
        //
        $this->addSql('ALTER TABLE documents DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE documents ADD sequence BIGINT NOT NULL');
        $this->addSql('UPDATE documents SET sequence = id');
        $this->addSql('ALTER TABLE documents ADD section LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, ADD date_found DATETIME DEFAULT NULL, ADD source_link VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD source_publisher_type VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD tags LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\', ADD source_title VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD source_description VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD permalink VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD main LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, ADD main_length INT DEFAULT NULL, ADD summary_text LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, ADD title VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD published DATETIME DEFAULT NULL, ADD publisher VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD links LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\', ADD author_name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD author_link VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD author_gender VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD image_src VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD sentiment VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD lang VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD country VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD state VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD city VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD shares INT DEFAULT NULL, ADD views INT DEFAULT NULL, ADD source_location VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD shared_identifier VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD point VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD duplicates_count INT DEFAULT NULL, ADD source_publisher_subtype VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD source_date_found DATETIME DEFAULT NULL, ADD source_hashcode VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP id, DROP platform, DROP data');
        $this->addSql('ALTER TABLE documents ADD PRIMARY KEY (sequence)');

        //
        // Create new foreign keys.
        //
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AC33F7837 FOREIGN KEY (document_id) REFERENCES documents (sequence)');
        $this->addSql('ALTER TABLE pages ADD CONSTRAINT FK_2074E575C33F7837 FOREIGN KEY (document_id) REFERENCES documents (sequence)');
        $this->addSql('ALTER TABLE deleted_documents ADD document_sequence BIGINT NOT NULL, DROP document_id');
        $this->addSql('ALTER TABLE deleted_documents ADD CONSTRAINT FK_A3B88FFDDD472672 FOREIGN KEY (document_sequence) REFERENCES documents (sequence)');
        $this->addSql('CREATE INDEX IDX_A3B88FFDDD472672 ON deleted_documents (document_sequence)');
        $this->addSql('ALTER TABLE deleted_documents ADD PRIMARY KEY (abstract_feed_id, document_sequence)');
    }
}
