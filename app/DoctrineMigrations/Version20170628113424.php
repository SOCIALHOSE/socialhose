<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170628113424 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE recently_used_feeds (id INT AUTO_INCREMENT NOT NULL, feed_id INT DEFAULT NULL, user_id INT DEFAULT NULL, used_at DATETIME NOT NULL, INDEX IDX_7329C41B51A5BC03 (feed_id), INDEX IDX_7329C41BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE recently_used_feeds ADD CONSTRAINT FK_7329C41B51A5BC03 FOREIGN KEY (feed_id) REFERENCES feeds (id)');
        $this->addSql('ALTER TABLE recently_used_feeds ADD CONSTRAINT FK_7329C41BA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE recently_used_feeds');
    }
}
