<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20201129180056 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cross_analytics_feeds DROP FOREIGN KEY FK_CDBB0E14B097194');
        $this->addSql('ALTER TABLE cross_analytics_feeds ADD CONSTRAINT FK_CDBB0E14B097194 FOREIGN KEY (analytic_context_hash) REFERENCES analytics_context (hash) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cross_analytics_feeds DROP FOREIGN KEY FK_CDBB0E14B097194');
        $this->addSql('ALTER TABLE cross_analytics_feeds ADD CONSTRAINT FK_CDBB0E14B097194 FOREIGN KEY (analytic_context_hash) REFERENCES analytics_context (hash)');
    }
}
