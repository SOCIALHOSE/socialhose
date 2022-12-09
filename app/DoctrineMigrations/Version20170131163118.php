<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170131163118 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE TABLE categories (
                id INT AUTO_INCREMENT NOT NULL,
                user_id INT DEFAULT NULL,
                INDEX IDX_3AF34668A76ED395 (user_id),
                name VARCHAR(255) NOT NULL,
                internal TINYINT(1) NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            ALTER TABLE categories
            ADD CONSTRAINT FK_3AF34668A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)
        ');
        $this->addSql('
            ALTER TABLE queries
            ADD category_id INT DEFAULT NULL,
            ADD name VARCHAR(255) DEFAULT NULL
        ');
        $this->addSql('
            ALTER TABLE queries
            ADD CONSTRAINT FK_8AF8477212469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)
        ');
        $this->addSql('CREATE INDEX IDX_8AF8477212469DE2 ON queries (category_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE queries DROP FOREIGN KEY FK_8AF8477212469DE2');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP INDEX IDX_8AF8477212469DE2 ON queries');
        $this->addSql('
            ALTER TABLE queries
            DROP category_id,
            DROP name
        ');
    }
}
