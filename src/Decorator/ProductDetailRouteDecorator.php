<?php declare(strict_types=1);

namespace SalesChannelSpecificContent\Decorator;

use SalesChannelSpecificContent\Subscriber\ProductSalesChannelContentSubscriber;
use Shopware\Core\Content\Product\SalesChannel\Detail\AbstractProductDetailRoute;
use Shopware\Core\Content\Product\SalesChannel\Detail\ProductDetailRouteResponse;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

class ProductDetailRouteDecorator extends AbstractProductDetailRoute
{
    private AbstractProductDetailRoute $decorated;
    private ProductSalesChannelContentSubscriber $contentSubscriber;

    public function __construct(AbstractProductDetailRoute $decorated, ProductSalesChannelContentSubscriber $contentSubscriber)
    {
        $this->decorated = $decorated;
        $this->contentSubscriber = $contentSubscriber;
    }

    public function getDecorated(): AbstractProductDetailRoute
    {
        return $this->decorated;
    }
    public function load(string $productId, Request $request, SalesChannelContext $context, Criteria $criteria): ProductDetailRouteResponse
    {
        $response = $this->decorated->load($productId, $request, $context, $criteria);

        $product = $response->getProduct();
        $customContent = $this->contentSubscriber->getSalesChannelContentWithFallback(
            $product->getId(),
            $product->getParentId(),
            $context->getSalesChannelId(),
            $context->getContext()
        );

        if ($customContent === null) {
            return $response;
        }

        $this->contentSubscriber->applyToProduct($product, $customContent);

        return $response;
    }
}