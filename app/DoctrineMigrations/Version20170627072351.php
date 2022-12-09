<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170627072351 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pages ADD clip_feed_id INT DEFAULT NULL, DROP score');
        $this->addSql('ALTER TABLE pages ADD CONSTRAINT FK_2074E575E3491210 FOREIGN KEY (clip_feed_id) REFERENCES feeds (id)');
        $this->addSql('CREATE INDEX IDX_2074E575E3491210 ON pages (clip_feed_id)');
        $this->addSql('ALTER TABLE feeds ADD filters LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', ADD total_count INT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE feeds DROP filters, DROP total_count');
        $this->addSql('ALTER TABLE pages DROP FOREIGN KEY FK_2074E575E3491210');
        $this->addSql('DROP INDEX IDX_2074E575E3491210 ON pages');
        $this->addSql('ALTER TABLE pages ADD score DOUBLE PRECISION NOT NULL, DROP clip_feed_id');
    }
}
