<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200524214344 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create `cistern` schema';
    }

    public function up(Schema $schema) : void
    {
        $cisternSchema = $schema->createTable('level');
        $cisternSchema
            ->addColumn('id', 'integer')
            ->setAutoincrement(true)
            ->setNotnull(true);
        $cisternSchema
            ->addColumn('liter', 'float')
            ->setNotnull(true);
        $cisternSchema
            ->addColumn('datetime', 'datetime')
            ->setNotnull(true)
            ->setDefault('CURRENT_TIMESTAMP');
    }

    public function down(Schema $schema) : void
    {

    }
}
