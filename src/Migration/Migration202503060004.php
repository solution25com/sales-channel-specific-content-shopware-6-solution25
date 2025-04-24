<?php declare(strict_types=1);

namespace SalesChannelSpecificContent\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration202503060004 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 202503060004;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            ALTER TABLE sales_channel_specific_content
            ADD COLUMN wholesale_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            ADD COLUMN retail_price DECIMAL(10,2) NOT NULL DEFAULT 0.00
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // No destructive changes needed
    }
}
