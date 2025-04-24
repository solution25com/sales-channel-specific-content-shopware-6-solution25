<?php declare(strict_types=1);

namespace SalesChannelSpecificContent\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration202503060002 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 202503060002;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            ALTER TABLE sales_channel_specific_content
            MODIFY COLUMN product_features TEXT NULL,
            MODIFY COLUMN whats_included TEXT NULL;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // No destructive changes needed
    }
}

 