<?php declare(strict_types=1);

namespace SalesChannelSpecificContent\Subscriber;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Framework\Api\Context\SalesChannelApiSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductSalesChannelContentSubscriber implements EventSubscriberInterface
{
    private EntityRepository $contentRepository;

    public function __construct(EntityRepository $contentRepository)
    {
        $this->contentRepository = $contentRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductEvents::PRODUCT_LOADED_EVENT => 'onProductLoaded',
        ];
    }

    public function onProductLoaded(EntityLoadedEvent $event): void
    {
        $context = $event->getContext();
        $salesChannelId = null;
        if ($context->getSource() instanceof SalesChannelApiSource) {
            $salesChannelId = $context->getSource()->getSalesChannelId();
        }

        if(!$salesChannelId) {
            return;
        }
        /** @var ProductEntity $product */
        foreach ($event->getEntities() as $product) {
            $customContent = $this->getSalesChannelContentWithFallback(
                $product->getId(),
                $product->getParentId(),
                $salesChannelId,
                $context
            );

            if ($customContent !== null) {
                $this->applyToProduct($product, $customContent);
            }
        }
    }

    public function getSalesChannelContentWithFallback(?string $productId, ?string $parentProductId, string $salesChannelId, Context $context): ?Entity
    {
        $productContent = $this->findContentByProductId($productId, $salesChannelId, $context);

        if (!$parentProductId) {
            return $productContent;
        }

        $parentContent = $this->findContentByProductId($parentProductId, $salesChannelId, $context);

        if ($productContent === null) {
            return $parentContent;
        }

        if ($parentContent !== null) {
            $this->applyFallbackValues($productContent, $parentContent);
        }

        return $productContent;
    }

    public function applyToProduct(ProductEntity $product, Entity $customContent): void
    {
        $product->addExtension('salesChannelSpecificContent', $customContent);

        $translated = $product->getTranslated();

        if (($productName = $this->getFilledString($customContent, 'productName')) !== null) {
            $translated['name'] = $productName;
            $product->setName($productName);
        }

        if (($shortDescription = $this->getFilledString($customContent, 'shortDescription')) !== null) {
            $translated['description'] = $shortDescription;
            $product->setDescription($shortDescription);
        }

        if (($metaTitle = $this->getFilledString($customContent, 'metaTitle')) !== null) {
            $translated['metaTitle'] = $metaTitle;
            $product->setMetaTitle($metaTitle);
        }

        if (($metaDescription = $this->getFilledString($customContent, 'metaDescription')) !== null) {
            $translated['metaDescription'] = $metaDescription;
            $product->setMetaDescription($metaDescription);
        }

        if (($metaKeywords = $this->getFilledString($customContent, 'metaKeywords')) !== null) {
            $translated['keywords'] = $metaKeywords;
            $product->setKeywords($metaKeywords);
        }

        $product->setTranslated($translated);
    }

    private function findContentByProductId(?string $productId, string $salesChannelId, Context $context): ?Entity
    {
        if (!$productId) {
            return null;
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));
        $criteria->addFilter(new EqualsFilter('productId', $productId));

        return $this->contentRepository->search($criteria, $context)->first();
    }

    private function applyFallbackValues(Entity $content, Entity $fallbackContent): void
    {
        $fallbackFields = [
            'productName',
            'shortDescription',
            'metaTitle',
            'metaDescription',
            'metaKeywords',
            'longDescription',
            'productFeatures',
            'whatsIncluded',
            'coverImageId',
        ];

        foreach ($fallbackFields as $field) {
            if ($this->getFilledString($content, $field) !== null) {
                continue;
            }

            $fallbackValue = $this->getFilledString($fallbackContent, $field);

            if ($fallbackValue !== null) {
                $content->assign([$field => $fallbackValue]);
            }
        }
    }

    private function getFilledString(Entity $customContent, string $field): ?string
    {
        $value = $customContent->get($field);

        if (!\is_string($value)) {
            return null;
        }

        $normalizedValue = trim(strip_tags(str_replace('&nbsp;', ' ', $value)));

        return $normalizedValue === '' ? null : $value;
    }
}
