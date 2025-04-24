<?php declare(strict_types=1);

namespace SalesChannelSpecificContent\Core\Content\ContentForSalesChannel;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(ContentForSalesChannelEntity $entity)
 * @method void set(string $key, ContentForSalesChannelEntity $entity)
 * @method ContentForSalesChannelEntity[] getIterator()
 * @method ContentForSalesChannelEntity[] getElements()
 * @method ContentForSalesChannelEntity|null get(string $key)
 * @method ContentForSalesChannelEntity|null first()
 * @method ContentForSalesChannelEntity|null last()
 */
class ContentForSalesChannelCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ContentForSalesChannelEntity::class;
    }
}