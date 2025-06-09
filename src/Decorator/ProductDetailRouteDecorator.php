<?php declare(strict_types=1);

namespace SalesChannelSpecificContent\Decorator;

use Shopware\Core\Content\Product\SalesChannel\Detail\AbstractProductDetailRoute;
use Shopware\Core\Content\Product\SalesChannel\Detail\ProductDetailRoute;
use Shopware\Core\Content\Product\SalesChannel\Detail\ProductDetailRouteResponse;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

class ProductDetailRouteDecorator extends AbstractProductDetailRoute
{
    private AbstractProductDetailRoute $decorated;
    private EntityRepository $contentRepository;

    public function __construct(AbstractProductDetailRoute $decorated, EntityRepository $contentRepository)
    {
        $this->decorated = $decorated;
        $this->contentRepository = $contentRepository;
    }

    public function getDecorated(): AbstractProductDetailRoute
    {
        return $this->decorated;
    }
    public function load(string $productId, Request $request, SalesChannelContext $context, Criteria $criteria): ProductDetailRouteResponse
    {
        $response = $this->decorated->load($productId, $request, $context, $criteria);

        $product = $response->getProduct();
        $productId = $product->getParentId() ?? $product->getId();

        $customCriteria = new Criteria();
        $customCriteria->addFilter(new EqualsFilter('salesChannelId', $context->getSalesChannelId()));
        $customCriteria->addFilter(new EqualsFilter('productId', $productId));

        $customContent = $this->contentRepository->search($customCriteria, $context->getContext())->first();

        if ($customContent !== null) {
            $product->setTranslated(array_merge(
                $product->getTranslated(),
                [
                    'name' => $customContent->get('metaTitle'),
                    'description' => $customContent->get('shortDescription') ?? '',
                    'metaTitle' => $customContent->get('metaTitle'),
                    'metaDescription' => $customContent->get('metaDescription'),
                    'metaKeywords' => $customContent->get('metaKeywords'),
                ]
            ));
        }

        return $response;
    }
}