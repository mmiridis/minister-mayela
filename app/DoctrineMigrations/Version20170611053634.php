<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170611053634 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE provider (id INT AUTO_INCREMENT NOT NULL, category VARCHAR(255) NOT NULL, phone VARCHAR(50) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, email VARCHAR(50) DEFAULT NULL, position INT NOT NULL, is_active TINYINT(1) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE provider_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_71F6C89D2C2AC5D3 (translatable_id), UNIQUE INDEX provider_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE provider_translation ADD CONSTRAINT FK_71F6C89D2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES provider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE faq ADD created DATETIME NOT NULL, ADD updated DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE provider_translation DROP FOREIGN KEY FK_71F6C89D2C2AC5D3');
        $this->addSql('DROP TABLE provider');
        $this->addSql('DROP TABLE provider_translation');
        $this->addSql('ALTER TABLE faq DROP created, DROP updated');
    }
}
