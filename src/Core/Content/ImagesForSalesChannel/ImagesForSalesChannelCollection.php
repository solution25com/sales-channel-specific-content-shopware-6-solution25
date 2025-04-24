<?php declare(strict_types=1);

namespace SalesChannelSpecificContent\Core\Content\ImagesForSalesChannel;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(ImagesForSalesChannelEntity $entity)
 * @method void set(string $key, ImagesForSalesChannelEntity $entity)
 * @method ImagesForSalesChannelEntity[] getIterator()
 * @method ImagesForSalesChannelEntity[] getElements()
 * @method ImagesForSalesChannelEntity|null get(string $key)
 * @method ImagesForSalesChannelEntity|null first()
 * @method ImagesForSalesChannelEntity|null last()
 */
class ImagesForSalesChannelCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ImagesForSalesChannelEntity::class;
    }
}