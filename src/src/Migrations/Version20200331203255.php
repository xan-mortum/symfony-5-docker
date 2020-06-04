<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200331203255 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A18C03F15C');
        $this->addSql('DROP INDEX IDX_5D9F75A18C03F15C ON employee');
        $this->addSql('ALTER TABLE employee ADD parent_id INT DEFAULT NULL, DROP employee_id');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1727ACA70 FOREIGN KEY (parent_id) REFERENCES employee (id)');
        $this->addSql('CREATE INDEX IDX_5D9F75A1727ACA70 ON employee (parent_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A1727ACA70');
        $this->addSql('DROP INDEX IDX_5D9F75A1727ACA70 ON employee');
        $this->addSql('ALTER TABLE employee ADD employee_id INT DEFAULT NULL, DROP parent_id');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A18C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('CREATE INDEX IDX_5D9F75A18C03F15C ON employee (employee_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
    }
}
