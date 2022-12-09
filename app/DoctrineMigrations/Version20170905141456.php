<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170905141456 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE deleted_documents (abstract_feed_id INT NOT NULL, document_sequence BIGINT NOT NULL, INDEX IDX_A3B88FFDF312DA93 (abstract_feed_id), INDEX IDX_A3B88FFDDD472672 (document_sequence), PRIMARY KEY(abstract_feed_id, document_sequence)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE deleted_documents ADD CONSTRAINT FK_A3B88FFDF312DA93 FOREIGN KEY (abstract_feed_id) REFERENCES feeds (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE deleted_documents ADD CONSTRAINT FK_A3B88FFDDD472672 FOREIGN KEY (document_sequence) REFERENCES documents (sequence)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE deleted_documents');
    }
}
