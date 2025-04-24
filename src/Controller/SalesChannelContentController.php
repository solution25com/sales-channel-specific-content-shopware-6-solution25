<?php

namespace SalesChannelSpecificContent\Controller;

use SalesChannelSpecificContent\Core\Service\SalesChannelContentService;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['api']])]
class SalesChannelContentController extends AbstractController
{
    private EntityRepository $contentRepository;
    private EntityRepository $imageRepository;

    public function __construct(EntityRepository $contentRepository, EntityRepository $imageRepository)
    {
        $this->contentRepository = $contentRepository;
        $this->imageRepository = $imageRepository;
    }
    #[Route('/api/product/sales-channel-content/{id}', name: 'api.get.content', methods: ['GET'])]
    public function get(string $id, Context $context)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productId', $id));

        $criteria->addAssociation('coverImage');
        $criteria->addAssociation('SalesChannelContentId');

        $content = $this->contentRepository->search($criteria, $context)->getEntities();
        //return status, message, data
        $return = [
            'status' => 'success',
            'message' => 'Sales Channel Specific Content and Images fetched successfully',
            'data' => $content
        ];
        return new JsonResponse($return);
    }

    #[Route('/api/product/sales-channel-content/{SalesChannelContentId}', name: 'api.sales_channel_specific_content.delete', methods: ['DELETE'])]
    public function deleteContent(string $SalesChannelContentId, Context $context): JsonResponse
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $SalesChannelContentId));
    
        $content = $this->contentRepository->search($criteria, $context)->first();
    
        if (!$content) {
            return new JsonResponse(['error' => 'Content not found'], 404);
        }
    
        $imageCriteria = new Criteria();
        $imageCriteria->addFilter(new EqualsFilter('SalesChannelContentId', $SalesChannelContentId));
        $images = $this->imageRepository->search($imageCriteria, $context)->getEntities();
    
        $imageDeleteData = [];
        foreach ($images as $image) {
            $imageDeleteData[] = ['id' => $image->get('id')]; 
        }
    
        if (!empty($imageDeleteData)) {
            $this->imageRepository->delete($imageDeleteData, $context);
        }
    
        $this->contentRepository->delete([['id' => $SalesChannelContentId]], $context);
    
        return new JsonResponse(['message' => 'Sales Channel Specific Content and Images deleted successfully']);
    }    
    
    #[Route('/api/product/sales-channel-content/{id}', name: 'api.sales_channel_specific_content.saveContent', methods: ['POST'])]
    public function saveContent(string $id, Request $request, Context $context): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data || !isset($data['extensions'])) {
            return new JsonResponse(['error' => 'Invalid data format'], 400);
        }

        $salesChannelContents = $data['extensions']['salesChannelSpecificContent'] ?? [];
        $salesChannelImages = $data['extensions']['salesChannelSpecificImages'] ?? [];

        $insertContentData = [];
        $insertImageData = [];

        foreach ($salesChannelContents as $content) {
            $contentId = $content['id'] ?? Uuid::randomHex();

            $insertContentData[] = [
                'id' => $contentId,
                'productId' => $id,
                'salesChannelId' => $content['salesChannelId'],
                'longDescription' => $content['longDescription'] ?? null,
                'metaDescription' => $content['metaDescription'] ?? null,
                'metaKeywords' => $content['metaKeywords'] ?? null,
                'metaTitle' => $content['metaTitle'] ?? null,
                'shortDescription' => $content['shortDescription'] ?? null,
                'productName' => $content['productName'] ?? null,
                'productFeatures' => $content['productFeatures'] ?? null,
                'whatsIncluded' => $content['whatsIncluded'] ?? null,
                'coverImageId' => $content['coverImageId'] ?? null,
                'wholesalePrice' => isset($content['wholesalePrice']) ? (float) $content['wholesalePrice'] : 0.00,
                'retailPrice' => isset($content['retailPrice']) ? (float) $content['retailPrice'] : 0.00,
            ];

            foreach ($salesChannelImages as $image) {
                if ($image['SalesChannelContentId'] === $contentId) {
                    $insertImageData[] = [
                        'id' => Uuid::randomHex(),
                        'SalesChannelContentId' => $contentId,
                        'mediaId' => $image['mediaId'],
                        'position' => $image['position'] ?? 0,
                    ];
                }
            }
        }

        if (!empty($insertContentData)) {
            $this->contentRepository->upsert($insertContentData, $context);
        }

        if (!empty($insertImageData)) {
            $this->imageRepository->upsert($insertImageData, $context);
        }

        return new JsonResponse(['message' => 'Sales Channel Specific Content and Images saved successfully']);
    }
}