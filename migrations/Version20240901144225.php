<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240901144225 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add title to draft an newsletter_entry';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE draft ADD title VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE newsletter_entry ADD title VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE newsletter_entry DROP title');
        $this->addSql('ALTER TABLE draft DROP title');
    }
}
