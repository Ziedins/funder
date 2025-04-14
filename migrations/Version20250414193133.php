<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250414193133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add initial customers with accounts';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            INSERT INTO customer (id, name) VALUES (100, "John");
            INSERT INTO customer (id, name) VALUES (101, "Doe");
            INSERT INTO currency (id, name, symbol) VALUES (100, "EUR", "€");
            INSERT INTO currency (id, name, symbol) VALUES (101, "USD", "$");
            INSERT INTO currency (id, name, symbol) VALUES (102, "JPY", "¥");
            INSERT INTO account (customer_id, currency_id, balance) VALUES (100, 100, 65.5);
            INSERT INTO account (customer_id, currency_id, balance) VALUES (101, 101, 73);
        SQL);

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
