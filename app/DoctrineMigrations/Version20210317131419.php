<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20210317131419 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE plan ADD user_id INT DEFAULT NULL, ADD is_plan_downgrade TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE plan ADD CONSTRAINT FK_DD5A5B7DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_DD5A5B7DA76ED395 ON plan (user_id)');
        $this->addSql('ALTER TABLE subscriptions ADD is_subscription_cancelled TINYINT(1) NOT NULL, ADD is_plan_downgrade TINYINT(1) NOT NULL, ADD start_date DATETIME DEFAULT NULL, ADD end_date DATETIME DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE plan DROP FOREIGN KEY FK_DD5A5B7DA76ED395');
        $this->addSql('DROP INDEX IDX_DD5A5B7DA76ED395 ON plan');
        $this->addSql('ALTER TABLE plan DROP user_id, DROP is_plan_downgrade');
        $this->addSql('ALTER TABLE subscriptions DROP is_subscription_cancelled, DROP is_plan_downgrade, DROP start_date, DROP end_date');
    }
}
