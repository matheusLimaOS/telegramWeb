<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251222002730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tips_revealed ADD guessed BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE tips_revealed ADD guess VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE tips_revealed ADD guess_right BOOLEAN DEFAULT false NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tips_revealed DROP guessed');
        $this->addSql('ALTER TABLE tips_revealed DROP guess');
        $this->addSql('ALTER TABLE tips_revealed DROP guess_right');
    }
}
