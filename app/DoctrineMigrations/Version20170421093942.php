<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170421093942 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Drop primary key and foreign key indexes.
        $this->addSql('ALTER TABLE pages DROP FOREIGN KEY FK_2074E575C33F7837');
        $this->addSql('ALTER TABLE documents DROP PRIMARY KEY');

        $this->addSql('ALTER TABLE documents CHANGE sequence sequence BIGINT NOT NULL');
        $this->addSql('ALTER TABLE pages CHANGE document_id document_id BIGINT DEFAULT NULL');

        // Return indexes back.
        $this->addSql('ALTER TABLE documents ADD PRIMARY KEY(sequence)');
        $this->addSql('
            ALTER TABLE pages
            ADD CONSTRAINT FK_2074E575C33F7837
            FOREIGN KEY (document_id) REFERENCES documents (sequence)
        ');
        $this->addSql('ALTER TABLE filters_values ADD expires_at DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Drop primary key and foreign key indexes.
        $this->addSql('ALTER TABLE pages DROP FOREIGN KEY FK_2074E575C33F7837');
        $this->addSql('ALTER TABLE documents DROP PRIMARY KEY');

        $this->addSql('ALTER TABLE documents CHANGE sequence sequence VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE pages CHANGE document_id document_id VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');

        // Return indexes back.
        $this->addSql('ALTER TABLE documents ADD PRIMARY KEY(sequence)');
        $this->addSql('
            ALTER TABLE pages
            ADD CONSTRAINT FK_2074E575C33F7837
            FOREIGN KEY (document_id) REFERENCES documents (sequence)
        ');
        $this->addSql('ALTER TABLE filters_values DROP expires_at');
    }
}
