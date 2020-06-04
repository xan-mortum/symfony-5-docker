<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200330181048 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE employee (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_5D9F75A112469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE employee_employee (employee_source INT NOT NULL, employee_target INT NOT NULL, INDEX IDX_CDA0246676BBBBE6 (employee_source), INDEX IDX_CDA024666F5EEB69 (employee_target), PRIMARY KEY(employee_source, employee_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE first_name (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A112469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE employee_employee ADD CONSTRAINT FK_CDA0246676BBBBE6 FOREIGN KEY (employee_source) REFERENCES employee (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee_employee ADD CONSTRAINT FK_CDA024666F5EEB69 FOREIGN KEY (employee_target) REFERENCES employee (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A112469DE2');
        $this->addSql('ALTER TABLE employee_employee DROP FOREIGN KEY FK_CDA0246676BBBBE6');
        $this->addSql('ALTER TABLE employee_employee DROP FOREIGN KEY FK_CDA024666F5EEB69');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE employee');
        $this->addSql('DROP TABLE employee_employee');
        $this->addSql('DROP TABLE first_name');
    }
}
