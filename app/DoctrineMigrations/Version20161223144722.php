<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161223144722 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            ALTER TABLE queries
            ADD user_id INT DEFAULT NULL,
            ADD type VARCHAR(255) NOT NULL,
            ADD limit_exceed TINYINT(1) DEFAULT NULL,
            ADD last_update_at DATETIME DEFAULT NULL,
            CHANGE expiration_date expiration_date DATETIME DEFAULT NULL
        ');
        $this->addSql('
            ALTER TABLE queries
            ADD CONSTRAINT FK_8AF84772A76ED395
            FOREIGN KEY (user_id) REFERENCES users (id)
        ');
        $this->addSql('CREATE INDEX IDX_8AF84772A76ED395 ON queries (user_id)');

        $this->addSql("
            UPDATE queries
            SET type = 'simple'
        ");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE queries DROP FOREIGN KEY FK_8AF84772A76ED395');
        $this->addSql('DROP INDEX IDX_8AF84772A76ED395 ON queries');
        $this->addSql('
            ALTER TABLE queries
            DROP user_id,
            DROP type,
            DROP limit_exceed,
            DROP last_update_at,
            CHANGE expiration_date expiration_date DATETIME NOT NULL
        ');
    }
}
