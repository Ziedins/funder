<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250406161942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE exchange_rate (id INT AUTO_INCREMENT NOT NULL, base_currency_id INT NOT NULL, target_currency_id INT NOT NULL, rate NUMERIC(10, 7) NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_E9521FAB3101778E (base_currency_id), INDEX IDX_E9521FABBF1ECE7C (target_currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE exchange_rate ADD CONSTRAINT FK_E9521FAB3101778E FOREIGN KEY (base_currency_id) REFERENCES currency (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE exchange_rate ADD CONSTRAINT FK_E9521FABBF1ECE7C FOREIGN KEY (target_currency_id) REFERENCES currency (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE exchange_rate DROP FOREIGN KEY FK_E9521FAB3101778E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE exchange_rate DROP FOREIGN KEY FK_E9521FABBF1ECE7C
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE exchange_rate
        SQL);
    }
}
