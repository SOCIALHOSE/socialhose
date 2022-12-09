<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170411131958 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE source_list ADD updated_by_id INT DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE source_list ADD CONSTRAINT FK_45427D1B896DBBDE FOREIGN KEY (updated_by_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_45427D1B896DBBDE ON source_list (updated_by_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE source_list DROP FOREIGN KEY FK_45427D1B896DBBDE');
        $this->addSql('DROP INDEX IDX_45427D1B896DBBDE ON source_list');
        $this->addSql('ALTER TABLE source_list DROP updated_by_id, CHANGE updated_at updated_at DATETIME NOT NULL');
    }
}
