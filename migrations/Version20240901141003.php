<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240901141003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create basic model representation';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE calendar_event_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE draft_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE newsletter_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE newsletter_entry_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE calendar_event (id INT NOT NULL, title VARCHAR(255) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, start_time TIME(0) WITHOUT TIME ZONE DEFAULT NULL, end_time TIME(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE draft (id INT NOT NULL, created_by_id INT NOT NULL, content TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_467C9694B03A8386 ON draft (created_by_id)');
        $this->addSql('CREATE TABLE newsletter (id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE newsletter_entry (id INT NOT NULL, created_by_id INT NOT NULL, newsletter_id INT NOT NULL, content TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A14CD184B03A8386 ON newsletter_entry (created_by_id)');
        $this->addSql('CREATE INDEX IDX_A14CD18422DB1917 ON newsletter_entry (newsletter_id)');
        $this->addSql('ALTER TABLE draft ADD CONSTRAINT FK_467C9694B03A8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE newsletter_entry ADD CONSTRAINT FK_A14CD184B03A8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE newsletter_entry ADD CONSTRAINT FK_A14CD18422DB1917 FOREIGN KEY (newsletter_id) REFERENCES newsletter (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE calendar_event_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE draft_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE newsletter_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE newsletter_entry_id_seq CASCADE');
        $this->addSql('ALTER TABLE draft DROP CONSTRAINT FK_467C9694B03A8386');
        $this->addSql('ALTER TABLE newsletter_entry DROP CONSTRAINT FK_A14CD184B03A8386');
        $this->addSql('ALTER TABLE newsletter_entry DROP CONSTRAINT FK_A14CD18422DB1917');
        $this->addSql('DROP TABLE calendar_event');
        $this->addSql('DROP TABLE draft');
        $this->addSql('DROP TABLE newsletter');
        $this->addSql('DROP TABLE newsletter_entry');
    }
}
