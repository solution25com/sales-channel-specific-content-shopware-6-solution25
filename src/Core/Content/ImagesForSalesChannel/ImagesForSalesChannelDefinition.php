<?php declare(strict_types=1);

namespace SalesChannelSpecificContent\Core\Content\ImagesForSalesChannel;

use SalesChannelSpecificContent\Core\Content\ContentForSalesChannel\ContentForSalesChannelDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;

class ImagesForSalesChannelDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'sales_channel_specific_images';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ImagesForSalesChannelEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ImagesForSalesChannelCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            new FkField('sales_channel_content_id', 'SalesChannelContentId', ContentForSalesChannelDefinition::class),
            new FkField('media_id', 'mediaId', MediaDefinition::class),
            new IntField('position', 'position'),

            new ManyToOneAssociationField('salesChannelContent', 'sales_channel_content_id', ContentForSalesChannelDefinition::class, 'id', false),
            new ManyToOneAssociationField('media', 'media_id', MediaDefinition::class, 'id', false),

            new CreatedAtField(),
            new UpdatedAtField(),
        ]);
    }
}