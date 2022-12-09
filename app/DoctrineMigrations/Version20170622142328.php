<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170622142328 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, document_id BIGINT DEFAULT NULL, author_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, create_at DATETIME NOT NULL, INDEX IDX_5F9E962AC33F7837 (document_id), INDEX IDX_5F9E962AF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AC33F7837 FOREIGN KEY (document_id) REFERENCES documents (sequence)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AF675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE comments');
    }
}
