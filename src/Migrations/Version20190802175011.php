<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190802175011 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user CHANGE partenair_id partenair_id INT NOT NULL, CHANGE telephone telephone INT NOT NULL');
        $this->addSql('DROP INDEX ninea ON partenaire');
        $this->addSql('ALTER TABLE operation ADD compte_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE operation ADD CONSTRAINT FK_1981A66DF2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id)');
        $this->addSql('CREATE INDEX IDX_1981A66DF2C56620 ON operation (compte_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operation DROP FOREIGN KEY FK_1981A66DF2C56620');
        $this->addSql('DROP INDEX IDX_1981A66DF2C56620 ON operation');
        $this->addSql('ALTER TABLE operation DROP compte_id');
        $this->addSql('CREATE UNIQUE INDEX ninea ON partenaire (ninea)');
        $this->addSql('ALTER TABLE user CHANGE partenair_id partenair_id INT DEFAULT NULL, CHANGE telephone telephone INT DEFAULT NULL');
    }
}
