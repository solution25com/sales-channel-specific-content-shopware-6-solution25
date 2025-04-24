<?php declare(strict_types=1);

namespace SalesChannelSpecificContent\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration202503060005 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 202503060005;
    }

    public function update(Connection $connection): void
    {

        //Fetch foreign key names dynamically
        $foreignKeys = $connection->fetchAllAssociative("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.TABLE_CONSTRAINTS 
                WHERE TABLE_NAME = 'sales_channel_specific_images' 
                AND CONSTRAINT_TYPE = 'FOREIGN KEY';
            ");

        //Drop existing foreign keys dynamically
        foreach ($foreignKeys as $fk) {
            $fkName = $fk['CONSTRAINT_NAME'];
            $connection->executeStatement("
                    ALTER TABLE sales_channel_specific_images 
                    DROP FOREIGN KEY `$fkName`;
                ");
        }

        //Remove duplicate records, keeping only the latest created_at entry
        $connection->executeStatement('
                DELETE FROM sales_channel_specific_content
                WHERE id NOT IN (
                    SELECT id
                    FROM (
                        SELECT id
                        FROM sales_channel_specific_content
                        WHERE (product_id, sales_channel_id, created_at) IN (
                            SELECT product_id, sales_channel_id, MAX(created_at) AS latest_created_at
                            FROM sales_channel_specific_content
                            GROUP BY product_id, sales_channel_id
                        )
                    ) AS keep_rows
                );
            ');

        //Add a new column for the updated id in sales_channel_specific_content
        $connection->executeStatement('
                ALTER TABLE sales_channel_specific_content
                ADD COLUMN new_id BINARY(16) NULL FIRST;
            ');

        //Generate new id values using MD5 hash (fits into BINARY(16))
        $connection->executeStatement('
                UPDATE sales_channel_specific_content
                SET new_id = UNHEX(MD5(CONCAT(product_id, sales_channel_id)));
            ');

        //Add a new temporary column for content_id in sales_channel_specific_images
        $connection->executeStatement('
                ALTER TABLE sales_channel_specific_images
                ADD COLUMN new_content_id BINARY(16) NULL;
            ');

        //Update sales_channel_specific_images to reference the new id
        $connection->executeStatement('
                UPDATE sales_channel_specific_images i
                JOIN sales_channel_specific_content c 
                ON i.sales_channel_content_id = c.id
                SET i.new_content_id = c.new_id;
            ');

        //Replace the old id column with the new_id in sales_channel_specific_content
        $connection->executeStatement('
                ALTER TABLE sales_channel_specific_content
                DROP PRIMARY KEY,
                DROP COLUMN id;
            ');

        //Move new_id to the first column and set it as the new primary key
        $connection->executeStatement('
                ALTER TABLE sales_channel_specific_content
                CHANGE COLUMN new_id id BINARY(16) NOT NULL FIRST,
                ADD PRIMARY KEY (id);
            ');

        //Add a unique constraint on (product_id, sales_channel_id)
        $connection->executeStatement('
                ALTER TABLE sales_channel_specific_content
                ADD UNIQUE INDEX ux_product_sales_channel (product_id, sales_channel_id);
            ');

        //Update sales_channel_specific_images to reference the new id
        $connection->executeStatement('
                ALTER TABLE sales_channel_specific_images
                DROP COLUMN sales_channel_content_id,
                CHANGE COLUMN new_content_id sales_channel_content_id BINARY(16) NOT NULL;
            ');

        //Re-add the foreign key constraints with the updated references
        $connection->executeStatement('
                ALTER TABLE sales_channel_specific_images 
                ADD CONSTRAINT fk_sales_channel_specific_images_content_id
                FOREIGN KEY (sales_channel_content_id)
                REFERENCES sales_channel_specific_content (id)
                ON DELETE CASCADE ON UPDATE CASCADE;
            ');

        $connection->executeStatement('
                ALTER TABLE sales_channel_specific_images 
                ADD CONSTRAINT fk_sales_channel_specific_images_media_id
                FOREIGN KEY (media_id)
                REFERENCES media (id)
                ON DELETE CASCADE ON UPDATE CASCADE;
            ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // No destructive changes needed in this case
    }
}