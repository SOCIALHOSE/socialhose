<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170219142028 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
          CREATE TABLE feeds (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT DEFAULT NULL,
            category_id INT DEFAULT NULL,
            query_id INT DEFAULT NULL,
            name VARCHAR(255) NOT NULL,
            type VARCHAR(255) NOT NULL,
            INDEX IDX_5A29F52FA76ED395 (user_id),
            INDEX IDX_5A29F52F12469DE2 (category_id),
            INDEX IDX_5A29F52FEF946F99 (query_id), PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('
          ALTER TABLE feeds
          ADD CONSTRAINT FK_5A29F52FA76ED395
          FOREIGN KEY (user_id)
          REFERENCES users (id)
        ');
        $this->addSql('
          ALTER TABLE feeds
          ADD CONSTRAINT FK_5A29F52F12469DE2
          FOREIGN KEY (category_id)
          REFERENCES categories (id)
        ');
        $this->addSql('
          ALTER TABLE feeds
          ADD CONSTRAINT FK_5A29F52FEF946F99
          FOREIGN KEY (query_id)
          REFERENCES queries (id)
        ');
        $this->addSql('DROP TABLE cross_users_stored_queries');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
          CREATE TABLE cross_users_stored_queries (
            id INT AUTO_INCREMENT NOT NULL,
            category_id INT DEFAULT NULL,
            user_id INT DEFAULT NULL,
            query_id INT DEFAULT NULL,
            name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci,
            INDEX IDX_A2E1F665EF946F99 (query_id),
            INDEX IDX_A2E1F665A76ED395 (user_id),
            INDEX IDX_A2E1F66512469DE2 (category_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('
          ALTER TABLE cross_users_stored_queries
          ADD CONSTRAINT FK_A2E1F66512469DE2
          FOREIGN KEY (category_id)
          REFERENCES categories (id)
        ');
        $this->addSql('
          ALTER TABLE cross_users_stored_queries
          ADD CONSTRAINT FK_A2E1F665A76ED395
          FOREIGN KEY (user_id)
          REFERENCES users (id)
        ');
        $this->addSql('
          ALTER TABLE cross_users_stored_queries
          ADD CONSTRAINT FK_A2E1F665EF946F99
          FOREIGN KEY (query_id)
          REFERENCES queries (id)
        ');
        $this->addSql('DROP TABLE feeds');
    }
}
