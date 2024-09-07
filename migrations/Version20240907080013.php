<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240907080013 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add sent to newsletter';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE newsletter ADD sent BOOLEAN DEFAULT false NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE newsletter DROP sent');
    }
}
