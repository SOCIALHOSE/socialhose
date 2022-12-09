<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170210123519 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE TABLE cross_users_stored_queries (
                id INT AUTO_INCREMENT NOT NULL,
                query_id INT DEFAULT NULL,
                user_id INT DEFAULT NULL,
                category_id INT DEFAULT NULL,
                name VARCHAR(255) NOT NULL,
                INDEX IDX_A2E1F665EF946F99 (query_id),
                INDEX IDX_A2E1F665A76ED395 (user_id),
                INDEX IDX_A2E1F66512469DE2 (category_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            ALTER TABLE cross_users_stored_queries
            ADD CONSTRAINT FK_A2E1F665EF946F99
            FOREIGN KEY (query_id)
            REFERENCES queries (id)
        ');
        $this->addSql('
            ALTER TABLE cross_users_stored_queries
            ADD CONSTRAINT FK_A2E1F665A76ED395
            FOREIGN KEY (user_id)
            REFERENCES users (id)
        ');
        $this->addSql('
            ALTER TABLE cross_users_stored_queries
            ADD CONSTRAINT FK_A2E1F66512469DE2
            FOREIGN KEY (category_id)
            REFERENCES categories (id)
        ');
        $this->addSql('ALTER TABLE queries DROP FOREIGN KEY FK_8AF84772A76ED395');
        $this->addSql('ALTER TABLE queries DROP FOREIGN KEY FK_8AF8477212469DE2');
        $this->addSql('DROP INDEX IDX_8AF84772A76ED395 ON queries');
        $this->addSql('DROP INDEX IDX_8AF8477212469DE2 ON queries');
        $this->addSql('ALTER TABLE queries DROP user_id, DROP category_id, DROP name');
        $this->addSql('ALTER TABLE source_list CHANGE title name VARCHAR(255) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE cross_users_stored_queries');
        $this->addSql('
            ALTER TABLE queries ADD user_id INT DEFAULT NULL,
            ADD category_id INT DEFAULT NULL,
            ADD name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci
        ');
        $this->addSql('
            ALTER TABLE queries
            ADD CONSTRAINT FK_8AF84772A76ED395
            FOREIGN KEY (user_id)
            REFERENCES users (id)
        ');
        $this->addSql('
            ALTER TABLE queries
            ADD CONSTRAINT FK_8AF8477212469DE2
            FOREIGN KEY (category_id)
            REFERENCES categories (id)
        ');
        $this->addSql('CREATE INDEX IDX_8AF84772A76ED395 ON queries (user_id)');
        $this->addSql('CREATE INDEX IDX_8AF8477212469DE2 ON queries (category_id)');
        $this->addSql('
            ALTER TABLE source_list
            CHANGE name title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci
        ');
    }
}
