<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170222065333 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users ADD master_user INT DEFAULT NULL');
        $this->addSql('
            ALTER TABLE users
            ADD CONSTRAINT FK_1483A5E9A626FB20
            FOREIGN KEY (master_user) REFERENCES users (id)'
        );
        $this->addSql('CREATE INDEX IDX_1483A5E9A626FB20 ON users (master_user)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9A626FB20');
        $this->addSql('DROP INDEX IDX_1483A5E9A626FB20 ON users');
        $this->addSql('ALTER TABLE users DROP master_user');
    }
}
