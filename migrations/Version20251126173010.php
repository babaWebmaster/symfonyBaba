<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251126173010 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE categories_site (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(75) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE maquette_categories_site (maquette_id INT NOT NULL, categories_site_id INT NOT NULL, INDEX IDX_74EA1DF11D1D3C76 (maquette_id), INDEX IDX_74EA1DF1DEB7C7E6 (categories_site_id), PRIMARY KEY(maquette_id, categories_site_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE maquette_categories_site ADD CONSTRAINT FK_74EA1DF11D1D3C76 FOREIGN KEY (maquette_id) REFERENCES maquette (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE maquette_categories_site ADD CONSTRAINT FK_74EA1DF1DEB7C7E6 FOREIGN KEY (categories_site_id) REFERENCES categories_site (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE maquette_categories_site DROP FOREIGN KEY FK_74EA1DF11D1D3C76
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE maquette_categories_site DROP FOREIGN KEY FK_74EA1DF1DEB7C7E6
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE categories_site
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE maquette_categories_site
        SQL);
    }
}
