<?php declare(strict_types=1);

namespace SalesChannelSpecificContent\Core\Content\ContentForSalesChannel;

use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FloatField; 
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;

class ContentForSalesChannelDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'sales_channel_specific_content';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ContentForSalesChannelEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ContentForSalesChannelCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('product_id', 'productId', ProductDefinition::class))->addFlags(new Required()),
            (new FkField('sales_channel_id', 'salesChannelId', SalesChannelDefinition::class))->addFlags(new Required()),
            (new LongTextField('long_description', 'longDescription'))->addFlags(new ApiAware(), new AllowHtml()),
            (new LongTextField('product_features', 'productFeatures'))->addFlags(new ApiAware(), new AllowHtml()),
            (new LongTextField('whats_included', 'whatsIncluded'))->addFlags(new ApiAware(), new AllowHtml()),

            (new LongTextField('meta_description', 'metaDescription'))->addFlags(new ApiAware(), new AllowHtml()),
            new StringField('meta_keywords', 'metaKeywords'),
            new StringField('meta_title', 'metaTitle'),
            (new LongTextField('short_description', 'shortDescription'))->addFlags(new ApiAware(), new AllowHtml()),
            new StringField('product_name', 'productName'),
            new FkField('cover_image_id', 'coverImageId', MediaDefinition::class),

            // Add new price fields
            (new FloatField('wholesale_price', 'wholesalePrice', 10, 2))->addFlags(new Required(), new ApiAware()),
            (new FloatField('retail_price', 'retailPrice', 10, 2))->addFlags(new Required(), new ApiAware()),

            new ManyToOneAssociationField('product', 'product_id', ProductDefinition::class, 'id', false),
            new ManyToOneAssociationField('salesChannel', 'sales_channel_id', SalesChannelDefinition::class, 'id', false),
            new ManyToOneAssociationField('coverImage', 'cover_image_id', MediaDefinition::class, 'id', false),

            new CreatedAtField(),
            new UpdatedAtField(),
        ]);
    }
}
