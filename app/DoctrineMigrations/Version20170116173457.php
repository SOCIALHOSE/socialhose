<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170116173457 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE TABLE jobs (
                id INT AUTO_INCREMENT NOT NULL,
                query_id INT DEFAULT NULL,
                INDEX IDX_A8936DC5EF946F99 (query_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            ALTER TABLE jobs
            ADD CONSTRAINT FK_A8936DC5EF946F99 FOREIGN KEY (query_id)
                REFERENCES queries (id)
        ');
        $this->addSql('ALTER TABLE queries ADD status VARCHAR(255) DEFAULT NULL');
        $this->addSql("
            UPDATE queries
            SET status = 'synced'
        ");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE jobs');
        $this->addSql('ALTER TABLE queries DROP status');
    }
}
