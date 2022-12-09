<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170214044904 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
          ALTER TABLE users
          ADD organization VARCHAR(255) NOT NULL,
          ADD expiration_day DATETIME DEFAULT NULL,
          ADD number_of_subscriber INT NOT NULL,
          ADD number_of_saved_fields_allowed INT NOT NULL,
          ADD number_of_newsletters_allowed INT NOT NULL,
          ADD number_of_searches_allowed INT NOT NULL
      ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            ALTER TABLE users
            DROP organization,
            DROP expiration_day,
            DROP number_of_subscriber,
            DROP number_of_saved_fields_allowed,
            DROP number_of_newsletters_allowed,
            DROP number_of_searches_allowed
        ');
    }
}
