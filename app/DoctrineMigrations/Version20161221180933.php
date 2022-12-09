<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161221180933 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE TABLE pages (
                id INT AUTO_INCREMENT NOT NULL,
                query_id INT DEFAULT NULL,
                document_id VARCHAR(255) DEFAULT NULL,
                number INT NOT NULL,
                INDEX IDX_2074E575EF946F99 (query_id),
                INDEX IDX_2074E575C33F7837 (document_id),
                PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8
            COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            CREATE TABLE queries (
                id INT AUTO_INCREMENT NOT NULL,
                string LONGTEXT NOT NULL,
                types LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\',
                unique_name VARCHAR(255) NOT NULL,
                date DATETIME NOT NULL,
                expiration_date DATETIME NOT NULL,
                total_count INT NOT NULL,
                PRIMARY KEY(id)
            )
             DEFAULT CHARACTER SET utf8
             COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('
            CREATE TABLE documents (
                sequence VARCHAR(255) NOT NULL,
                bucket BIGINT DEFAULT NULL,
                sequence_range BIGINT DEFAULT NULL,
                hashcode LONGTEXT DEFAULT NULL,
                resource LONGTEXT DEFAULT NULL,
                section VARCHAR(255) DEFAULT NULL,
                date_found DATETIME DEFAULT NULL,
                index_method VARCHAR(255) DEFAULT NULL,
                detection_method VARCHAR(255) DEFAULT NULL,
                version VARCHAR(255) DEFAULT NULL,
                source_hashcode VARCHAR(255) DEFAULT NULL,
                source_resource VARCHAR(255) DEFAULT NULL,
                source_link VARCHAR(255) DEFAULT NULL,
                source_publisher_type VARCHAR(255) DEFAULT NULL,
                source_date_found DATETIME DEFAULT NULL,
                source_last_updated DATETIME DEFAULT NULL,
                source_last_published DATETIME DEFAULT NULL,
                source_last_posted DATETIME DEFAULT NULL,
                source_update_interval BIGINT DEFAULT NULL,
                source_http_status INT DEFAULT NULL,
                source_content_length INT DEFAULT NULL,
                source_content_checksum VARCHAR(255) DEFAULT NULL,
                source_assigned_tags VARCHAR(255) DEFAULT NULL,
                strategy_source_setting_update VARCHAR(255) DEFAULT NULL,
                strategy_source_setting_index VARCHAR(255) DEFAULT NULL,
                source_title VARCHAR(255) DEFAULT NULL,
                source_description VARCHAR(255) DEFAULT NULL,
                source_feed_href VARCHAR(255) DEFAULT NULL,
                source_feed_title VARCHAR(255) DEFAULT NULL,
                source_feed_format VARCHAR(255) DEFAULT NULL,
                permalink VARCHAR(255) DEFAULT NULL,
                permalink_redirect VARCHAR(255) DEFAULT NULL,
                permalink_redirect_domain VARCHAR(255) DEFAULT NULL,
                permalink_redirect_site VARCHAR(255) DEFAULT NULL,
                canonical VARCHAR(255) DEFAULT NULL,
                domain VARCHAR(255) DEFAULT NULL,
                site VARCHAR(255) DEFAULT NULL,
                main LONGTEXT DEFAULT NULL,
                main_length INT DEFAULT NULL,
                main_checksum VARCHAR(255) DEFAULT NULL,
                main_format VARCHAR(255) DEFAULT NULL,
                extract LONGTEXT DEFAULT NULL,
                extract_length INT DEFAULT NULL,
                extract_checksum VARCHAR(255) DEFAULT NULL,
                summary_text LONGTEXT DEFAULT NULL,
                title VARCHAR(255) DEFAULT NULL,
                published DATETIME DEFAULT NULL,
                publisher VARCHAR(255) DEFAULT NULL,
                description VARCHAR(255) DEFAULT NULL,
                html LONGTEXT DEFAULT NULL,
                html_length INT DEFAULT NULL,
                links VARCHAR(255) DEFAULT NULL,
                author_name VARCHAR(255) DEFAULT NULL,
                author_link VARCHAR(255) DEFAULT NULL,
                author_gender VARCHAR(255) DEFAULT NULL,
                image_src VARCHAR(255) DEFAULT NULL,
                card VARCHAR(255) DEFAULT NULL,
                type VARCHAR(255) DEFAULT NULL,
                sentiment VARCHAR(255) DEFAULT NULL,
                lang VARCHAR(255) DEFAULT NULL,
                metadata_score INT DEFAULT NULL,
                PRIMARY KEY(sequence)
            )
            DEFAULT CHARACTER SET utf8
            COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('
            CREATE TABLE users (
                id INT AUTO_INCREMENT NOT NULL,
                username VARCHAR(180) NOT NULL,
                username_canonical VARCHAR(180) NOT NULL,
                email VARCHAR(180) NOT NULL,
                email_canonical VARCHAR(180) NOT NULL,
                enabled TINYINT(1) NOT NULL,
                salt VARCHAR(255) DEFAULT NULL,
                password VARCHAR(255) NOT NULL,
                last_login DATETIME DEFAULT NULL,
                confirmation_token VARCHAR(180) DEFAULT NULL,
                password_requested_at DATETIME DEFAULT NULL,
                roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\',
                UNIQUE INDEX UNIQ_1483A5E992FC23A8 (username_canonical),
                UNIQUE INDEX UNIQ_1483A5E9A0D96FBF (email_canonical),
                UNIQUE INDEX UNIQ_1483A5E9C05FB297 (confirmation_token),
                PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8
            COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            ALTER TABLE pages
            ADD CONSTRAINT FK_2074E575EF946F99
            FOREIGN KEY (query_id) REFERENCES queries (id)
        ');
        $this->addSql('
            ALTER TABLE pages
            ADD CONSTRAINT FK_2074E575C33F7837
            FOREIGN KEY (document_id) REFERENCES documents (sequence)
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pages DROP FOREIGN KEY FK_2074E575EF946F99');
        $this->addSql('ALTER TABLE pages DROP FOREIGN KEY FK_2074E575C33F7837');
        $this->addSql('DROP TABLE pages');
        $this->addSql('DROP TABLE queries');
        $this->addSql('DROP TABLE documents');
        $this->addSql('DROP TABLE users');
    }
}
