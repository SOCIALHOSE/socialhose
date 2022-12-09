<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170810124714 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE jobs');
        $this->addSql('ALTER TABLE subscriptions DROP FOREIGN KEY FK_4778A01A76ED395');
        $this->addSql('DROP INDEX IDX_4778A01A76ED395 ON subscriptions');
        $this->addSql('ALTER TABLE subscriptions CHANGE user_id owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subscriptions ADD CONSTRAINT FK_4778A017E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_4778A017E3C61F9 ON subscriptions (owner_id)');
        $this->addSql('ALTER TABLE recipients DROP FOREIGN KEY FK_146632C47E3C61F9');
        $this->addSql('ALTER TABLE recipients ADD CONSTRAINT FK_146632C47E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9CF9564CB');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9CF9564CB FOREIGN KEY (billing_subscription_id) REFERENCES subscriptions (id) ON DELETE SET NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE jobs (id INT AUTO_INCREMENT NOT NULL, query_id INT DEFAULT NULL, INDEX IDX_A8936DC5EF946F99 (query_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE jobs ADD CONSTRAINT FK_A8936DC5EF946F99 FOREIGN KEY (query_id) REFERENCES queries (id)');
        $this->addSql('ALTER TABLE recipients DROP FOREIGN KEY FK_146632C47E3C61F9');
        $this->addSql('ALTER TABLE recipients ADD CONSTRAINT FK_146632C47E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE subscriptions DROP FOREIGN KEY FK_4778A017E3C61F9');
        $this->addSql('DROP INDEX IDX_4778A017E3C61F9 ON subscriptions');
        $this->addSql('ALTER TABLE subscriptions CHANGE owner_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subscriptions ADD CONSTRAINT FK_4778A01A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_4778A01A76ED395 ON subscriptions (user_id)');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9CF9564CB');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9CF9564CB FOREIGN KEY (billing_subscription_id) REFERENCES subscriptions (id)');
    }
}
