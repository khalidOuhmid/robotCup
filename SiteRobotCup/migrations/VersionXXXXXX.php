<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class VersionXXXXXX extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add round column to T_ENCOUNTER_ENC table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE T_ENCOUNTER_ENC ADD ENC_ROUND SMALLINT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE T_ENCOUNTER_ENC DROP COLUMN ENC_ROUND');
    }
}
