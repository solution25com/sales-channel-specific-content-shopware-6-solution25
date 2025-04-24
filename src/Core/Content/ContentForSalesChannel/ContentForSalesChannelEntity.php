<?php declare(strict_types=1);

namespace SalesChannelSpecificContent\Core\Content\ContentForSalesChannel;

use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class ContentForSalesChannelEntity extends Entity
{
    use EntityIdTrait;

    protected ?string $productId;
    protected ?string $salesChannelId;
    protected ?string $longDescription;
    protected ?string $metaDescription;
    protected ?string $metaKeywords;
    protected ?string $metaTitle;
    protected ?string $shortDescription;
    protected ?string $productName;
    
    protected ?string $productFeatures = ''; 

    protected ?string $whatsIncluded;
    protected ?string $coverImageId;
    protected ?ProductEntity $product;
    protected ?SalesChannelEntity $salesChannel;
    protected ?MediaEntity $coverImage;

    protected ?float $wholesalePrice;
    protected ?float $retailPrice;

    public function getProductId(): ?string
    {
        return $this->productId;
    }

    public function setProductId(?string $productId): void
    {
        $this->productId = $productId;
    }

    public function getSalesChannelId(): ?string
    {
        return $this->salesChannelId;
    }

    public function setSalesChannelId(?string $salesChannelId): void
    {
        $this->salesChannelId = $salesChannelId;
    }

    public function getLongDescription(): ?string
    {
        return $this->longDescription;
    }

    public function setLongDescription(?string $longDescription): void
    {
        $this->longDescription = $longDescription;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }

    public function getMetaKeywords(): ?string
    {
        return $this->metaKeywords;
    }

    public function setMetaKeywords(?string $metaKeywords): void
    {
        $this->metaKeywords = $metaKeywords;
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): void
    {
        $this->metaTitle = $metaTitle;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): void
    {
        $this->shortDescription = $shortDescription;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(?string $productName): void
    {
        $this->productName = $productName;
    }

    public function getProductFeatures(): ?string
    {
        return $this->productFeatures;
    }

    public function setProductFeatures(?string $productFeatures): void
    {
        $this->productFeatures = $productFeatures;
    }

    public function getWhatsIncluded(): ?string
    {
        return $this->whatsIncluded;
    }

    public function setWhatsIncluded(?string $whatsIncluded): void
    {
        $this->whatsIncluded = $whatsIncluded;
    }

    public function getCoverImageId(): ?string
    {
        return $this->coverImageId;
    }

    public function setCoverImageId(?string $coverImageId): void
    {
        $this->coverImageId = $coverImageId;
    }

    public function getProduct(): ?ProductEntity
    {
        return $this->product;
    }

    public function setProduct(?ProductEntity $product): void
    {
        $this->product = $product;
    }

    public function getSalesChannel(): ?SalesChannelEntity
    {
        return $this->salesChannel;
    }

    public function setSalesChannel(?SalesChannelEntity $salesChannel): void
    {
        $this->salesChannel = $salesChannel;
    }

    public function getCoverImage(): ?MediaEntity
    {
        return $this->coverImage;
    }

    public function setCoverImage(?MediaEntity $coverImage): void
    {
        $this->coverImage = $coverImage;
    }
    public function getWholesalePrice(): ?float
    {
        return $this->wholesalePrice;
    }

    public function setWholesalePrice(?float $wholesalePrice): void
    {
        $this->wholesalePrice = $wholesalePrice;
    }

    public function getRetailPrice(): ?float
    {
        return $this->retailPrice;
    }

    public function setRetailPrice(?float $retailPrice): void
    {
        $this->retailPrice = $retailPrice;
    }
}