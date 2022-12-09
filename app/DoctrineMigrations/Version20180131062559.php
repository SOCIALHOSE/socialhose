<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180131062559 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE analytics_context (hash VARCHAR(255) NOT NULL, filters LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', raw_filters LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', PRIMARY KEY(hash)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cross_analytics_feeds (analytic_context_hash VARCHAR(255) NOT NULL, feed_id INT NOT NULL, INDEX IDX_CDBB0E14B097194 (analytic_context_hash), INDEX IDX_CDBB0E151A5BC03 (feed_id), PRIMARY KEY(analytic_context_hash, feed_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE analytics (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, context_id VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_EAC2E6887E3C61F9 (owner_id), INDEX IDX_EAC2E6886B00C1CF (context_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cross_analytics_feeds ADD CONSTRAINT FK_CDBB0E14B097194 FOREIGN KEY (analytic_context_hash) REFERENCES analytics_context (hash)');
        $this->addSql('ALTER TABLE cross_analytics_feeds ADD CONSTRAINT FK_CDBB0E151A5BC03 FOREIGN KEY (feed_id) REFERENCES feeds (id)');
        $this->addSql('ALTER TABLE analytics ADD CONSTRAINT FK_EAC2E6887E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE analytics ADD CONSTRAINT FK_EAC2E6886B00C1CF FOREIGN KEY (context_id) REFERENCES analytics_context (hash)');
        $this->addSql('ALTER TABLE subscriptions ADD analytics TINYINT(1) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cross_analytics_feeds DROP FOREIGN KEY FK_CDBB0E14B097194');
        $this->addSql('ALTER TABLE analytics DROP FOREIGN KEY FK_EAC2E6886B00C1CF');
        $this->addSql('DROP TABLE analytics_context');
        $this->addSql('DROP TABLE cross_analytics_feeds');
        $this->addSql('DROP TABLE analytics');
        $this->addSql('ALTER TABLE subscriptions DROP analytics');
    }
}
