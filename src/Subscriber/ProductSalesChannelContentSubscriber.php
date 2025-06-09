<?php declare(strict_types=1);

namespace SalesChannelSpecificContent\Subscriber;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Api\Context\SalesChannelApiSource;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Shopware\Core\Framework\DataAbstractionLayer\Pricing\PriceCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\Price as DALPrice;

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
            $productId = $product->getParentId() ?? $product->getId();

            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));
            $criteria->addFilter(new EqualsFilter('productId', $productId));
            $customContent = $this->contentRepository->search($criteria, $context)->first();

            if ($customContent !== null) {
                $product->setTranslated(array_merge(
                    $product->getTranslated(),
                    [
                        'name' => $customContent->get('productName'),
                        'description' => $customContent->get('shortDescription') ?? '',
                        'metaTitle' => $customContent->get('metaTitle'),
                        'metaDescription' => $customContent->get('metaDescription'),
                        'metaKeywords' => $customContent->get('metaKeywords'),
                    ]
                ));
                $product->setName($customContent->get('productName'));
                $product->setDescription($customContent->get('shortDescription'));
                $product->setMetaTitle($customContent->get('metaTitle'));
                $product->setMetaDescription($customContent->get('metaDescription'));

                $originalPriceCollection = $product->getPrice();
                $originalPrice = $originalPriceCollection ? $originalPriceCollection->first() : null;

                $net = $customContent->get('wholesalePrice') ?? ($originalPrice ? $originalPrice->getNet() : null);
                $gross = $customContent->get('retailPrice') ?? ($originalPrice ? $originalPrice->getGross() : null);

                if ($net !== null && $gross !== null) {
                    $currencyId = $originalPrice ? $originalPrice->getCurrencyId() : Defaults::CURRENCY;
                    $price = new DALPrice($currencyId, (float)$gross, (float)$net, true);
                    $product->setPrice(new PriceCollection([$price]));
                }
            }
        }
    }
}