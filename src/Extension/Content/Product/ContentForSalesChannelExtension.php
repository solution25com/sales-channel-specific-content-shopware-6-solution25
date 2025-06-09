<?php declare(strict_types=1);

namespace SalesChannelSpecificContent\Extension\Content\Product;

use SalesChannelSpecificContent\Core\Content\ContentForSalesChannel\ContentForSalesChannelDefinition;
use SalesChannelSpecificContent\Core\Content\ImagesForSalesChannel\ImagesForSalesChannelDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ContentForSalesChannelExtension extends EntityExtension
{

    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToManyAssociationField('customProductContent', ContentForSalesChannelDefinition::class, 'product_id'))
        );
        $collection->add(
            (new OneToManyAssociationField(
                'customProductImages',
                ImagesForSalesChannelDefinition::class,
                'sales_channel_content_id'
            ))
        );
    }

    /**
     * @inheritDoc
     */
    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }
}