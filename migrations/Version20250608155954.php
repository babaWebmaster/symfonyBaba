<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250608155954 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE seo_metadata (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, og_title VARCHAR(255) DEFAULT NULL, og_description LONGTEXT DEFAULT NULL, og_image VARCHAR(255) DEFAULT NULL, schema_json LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_AEB395364B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE seo_metadata ADD CONSTRAINT FK_AEB395364B89032C FOREIGN KEY (post_id) REFERENCES post (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE seo_metadata DROP FOREIGN KEY FK_AEB395364B89032C
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE seo_metadata
        SQL);
    }
}
