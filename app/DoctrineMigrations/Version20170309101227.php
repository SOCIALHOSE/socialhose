<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170309101227 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE saved_analyse ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE saved_analyse ADD CONSTRAINT FK_BF1AC3E9A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_BF1AC3E9A76ED395 ON saved_analyse (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE saved_analyse DROP FOREIGN KEY FK_BF1AC3E9A76ED395');
        $this->addSql('DROP INDEX IDX_BF1AC3E9A76ED395 ON saved_analyse');
        $this->addSql('ALTER TABLE saved_analyse DROP user_id');
    }
}
