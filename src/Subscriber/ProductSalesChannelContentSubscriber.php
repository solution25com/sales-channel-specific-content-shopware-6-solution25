<?php declare(strict_types=1);

namespace SalesChannelSpecificContent\Subscriber;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
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
            ProductPageLoadedEvent::class => 'onProductPageLoaded',
        ];
    }

    public function onProductPageLoaded(ProductPageLoadedEvent $event): void
    {
        $page = $event->getPage();
        $salesChannelId = $event->getSalesChannelContext()->getSalesChannelId();
        $product = $page->getProduct();
        $productId = $product->getParentId() ?? $product->getId();

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId)); // Ensure correct column name
        $criteria->addFilter(new EqualsFilter('productId', $productId));

        $customContent = $this->contentRepository->search($criteria, $event->getSalesChannelContext()->getContext())->first();

        if ($customContent !== null) {
            $page->addArrayExtension('salesChannelContents', [$customContent]);
        }
//        dd($event->getPage()->getExtensions());
    }

}