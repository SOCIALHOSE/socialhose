<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170223051007 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE source_list DROP FOREIGN KEY FK_45427D1BA76ED395');
        $this->addSql('
            ALTER TABLE source_list
            ADD CONSTRAINT FK_45427D1BA76ED395
            FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
        ');
        $this->addSql('CREATE INDEX source_search_idx ON sources (title, type)');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9A626FB20');
        $this->addSql('
            ALTER TABLE users
            ADD CONSTRAINT FK_1483A5E9A626FB20
            FOREIGN KEY (master_user) REFERENCES users (id) ON DELETE CASCADE
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE source_list DROP FOREIGN KEY FK_45427D1BA76ED395');
        $this->addSql('
            ALTER TABLE source_list
            ADD CONSTRAINT FK_45427D1BA76ED395
            FOREIGN KEY (user_id) REFERENCES users (id)
        ');
        $this->addSql('DROP INDEX source_search_idx ON sources');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9A626FB20');
        $this->addSql('
            ALTER TABLE users
            ADD CONSTRAINT FK_1483A5E9A626FB20
            FOREIGN KEY (master_user) REFERENCES users (id)
        ');
    }
}
