<?php declare(strict_types=1);

namespace SalesChannelSpecificContent\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration2025030601 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 2025030601;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            ALTER TABLE sales_channel_specific_content
            MODIFY COLUMN product_features LONGTEXT NULL,
            MODIFY COLUMN whats_included LONGTEXT NULL,
            MODIFY COLUMN short_description LONGTEXT NULL,
            MODIFY COLUMN meta_description LONGTEXT NULL
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // No destructive changes needed
    }
}

 