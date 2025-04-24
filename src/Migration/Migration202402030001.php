<?php declare(strict_types=1);

namespace SalesChannelSpecificContent\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration202402030001 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 202402030001;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE `sales_channel_specific_content` (
                `id` BINARY(16) NOT NULL,
                `product_id` BINARY(16) NOT NULL,
                `sales_channel_id` BINARY(16) NOT NULL,
                `long_description` LONGTEXT NULL,
                `meta_description` VARCHAR(255) NULL,
                `meta_keywords` VARCHAR(255) NULL,
                `meta_title` VARCHAR(255) NULL,
                `short_description` TEXT NULL,
                `product_name` VARCHAR(255) NULL,
                `whats_included` TEXT NULL,
                `cover_image_id` BINARY(16) NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `fk.sales_channel_specific_content.product_id` FOREIGN KEY (`product_id`)
                    REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.sales_channel_specific_content.sales_channel_id` FOREIGN KEY (`sales_channel_id`)
                    REFERENCES `sales_channel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.sales_channel_specific_content.cover_image_id` FOREIGN KEY (`cover_image_id`)
                    REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
            )
            ENGINE = InnoDB
            DEFAULT CHARSET = utf8mb4
            COLLATE = utf8mb4_unicode_ci;
        ');

        $connection->executeStatement('
            CREATE TABLE `sales_channel_specific_images` (
                `id` BINARY(16) NOT NULL,
                `sales_channel_content_id` BINARY(16) NOT NULL,
                `media_id` BINARY(16) NOT NULL,
                `position` INT NOT NULL DEFAULT 0,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `fk.sales_channel_specific_images.content_id` FOREIGN KEY (`sales_channel_content_id`)
                    REFERENCES `sales_channel_specific_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.sales_channel_specific_images.media_id` FOREIGN KEY (`media_id`)
                    REFERENCES `media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            )
            ENGINE = InnoDB
            DEFAULT CHARSET = utf8mb4
            COLLATE = utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        $connection->executeStatement('DROP TABLE IF EXISTS `sales_channel_specific_images`');
        $connection->executeStatement('DROP TABLE IF EXISTS `sales_channel_specific_content`');
    }
}