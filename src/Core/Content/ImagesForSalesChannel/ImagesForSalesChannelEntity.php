<?php declare(strict_types=1);

namespace SalesChannelSpecificContent\Core\Content\ImagesForSalesChannel;

use SalesChannelSpecificContent\Core\Content\ContentForSalesChannel\ContentForSalesChannelEntity;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ImagesForSalesChannelEntity extends Entity
{
    use EntityIdTrait;

    protected string $SalesChannelContentId;
    protected string $mediaId;
    protected int $position;

    protected ?ContentForSalesChannelEntity $salesChannelContent = null;
    protected ?MediaEntity $media = null;

    public function getSalesChannelContentId(): string
    {
        return $this->SalesChannelContentId;
    }

    public function setSalesChannelContentId(string $SalesChannelContentId): void
    {
        $this->SalesChannelContentId = $SalesChannelContentId;
    }

    public function getMediaId(): string
    {
        return $this->mediaId;
    }

    public function setMediaId(string $mediaId): void
    {
        $this->mediaId = $mediaId;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getsalesChannelContent(): ?ContentForSalesChannelEntity
    {
        return $this->salesChannelContent;
    }

    public function setsalesChannelContent(?ContentForSalesChannelEntity $salesChannelContent): void
    {
        $this->salesChannelContent = $salesChannelContent;
    }

    public function getMedia(): ?MediaEntity
    {
        return $this->media;
    }

    public function setMedia(?MediaEntity $media): void
    {
        $this->media = $media;
    }
}