<?php declare(strict_types=1);

namespace SalesChannelSpecificContent\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration2024021000 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 2024021000;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            ALTER TABLE sales_channel_specific_content
            ADD COLUMN product_features JSON NULL AFTER product_name;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // No destructive changes needed
    }
}
