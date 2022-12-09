<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170816121934 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D37E3C61F9');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D37E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE subscriptions DROP FOREIGN KEY FK_4778A017E3C61F9');
        $this->addSql('ALTER TABLE subscriptions ADD CONSTRAINT FK_4778A017E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9E92F8F78');
        $this->addSql('ALTER TABLE users DROP number_of_subscribers, DROP number_of_saved_fields_allowed, DROP number_of_newsletters_allowed, DROP number_of_searches_per_day_allowed, DROP allow_to_create_saved_feeds');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9E92F8F78 FOREIGN KEY (recipient_id) REFERENCES recipients (id) ON DELETE SET NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D37E3C61F9');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D37E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE subscriptions DROP FOREIGN KEY FK_4778A017E3C61F9');
        $this->addSql('ALTER TABLE subscriptions ADD CONSTRAINT FK_4778A017E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9E92F8F78');
        $this->addSql('ALTER TABLE users ADD number_of_subscribers INT NOT NULL, ADD number_of_saved_fields_allowed INT NOT NULL, ADD number_of_newsletters_allowed INT NOT NULL, ADD number_of_searches_per_day_allowed INT NOT NULL, ADD allow_to_create_saved_feeds TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9E92F8F78 FOREIGN KEY (recipient_id) REFERENCES recipients (id)');
    }
}
