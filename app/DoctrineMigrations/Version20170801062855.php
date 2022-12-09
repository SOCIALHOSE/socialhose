<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170801062855 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL, plan_id INT NOT NULL, type INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plan (id INT AUTO_INCREMENT NOT NULL, limitPlan INT NOT NULL, price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE user_organization');
        $this->addSql('ALTER TABLE users DROP billing_plan_id');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_organization (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, organization_id INT NOT NULL, roles VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, billing_plan VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE plan');
        $this->addSql('ALTER TABLE notifications CHANGE notification_type notification_type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE theme_type theme_type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE timezone timezone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE users ADD billing_plan_id INT NOT NULL');
    }
}
