<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170130165430 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE TABLE sessions (
                sess_id INT AUTO_INCREMENT NOT NULL,
                sess_data LONGTEXT NOT NULL COLLATE utf8_unicode_ci,
                sess_time DATETIME NOT NULL,
                PRIMARY KEY(sess_id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            CREATE TABLE refresh_tokens (
                id INT AUTO_INCREMENT NOT NULL,
                refresh_token VARCHAR(128) NOT NULL,
                username VARCHAR(255) NOT NULL,
                valid DATETIME NOT NULL,
                UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sessions');
        $this->addSql('DROP TABLE refresh_tokens');
    }
}
