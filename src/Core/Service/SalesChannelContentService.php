<?php

namespace SalesChannelSpecificContent\Core\Service;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;

class SalesChannelContentService
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Saves or updates Sales Channel Content and Images.
     */
    public function saveContent(string $productId, array $data): JsonResponse
    {
        if (!$data || !isset($data['extensions'])) {
            return new JsonResponse(['error' => 'Invalid data format'], 400);
        }

        $salesChannelContents = $data['extensions']['salesChannelSpecificContent'] ?? [];
        $salesChannelImages = $data['extensions']['salesChannelSpecificImages'] ?? [];

        try {
            foreach ($salesChannelContents as $content) {
                $contentId = isset($content['id']) ? Uuid::fromHexToBytes( $content['id']) : Uuid::fromHexToBytes(Uuid::randomHex());

                //  Check if content exists
                $existsQuery = $this->connection->createQueryBuilder()
                    ->select('id')
                    ->from('sales_channel_specific_content')
                    ->where('id = :id')
                    ->setParameter('id', $contentId)
                    ->executeQuery()
                    ->fetchOne();

                if ($existsQuery) {
                    //  Update existing content
                    $this->connection->createQueryBuilder()
                        ->update('sales_channel_specific_content')
                        ->set('long_description', ':long_description')
                        ->set('meta_description', ':meta_description')
                        ->set('meta_keywords', ':meta_keywords')
                        ->set('meta_title', ':meta_title')
                        ->set('short_description', ':short_description')
                        ->set('product_name', ':product_name')
                        ->set('whats_included', ':whats_included')
                        ->set('cover_image_id', ':cover_image_id')
                        ->set('updated_at', 'NOW()')
                        ->where('id = :id')
                        ->setParameter('id', $contentId)
                        ->setParameter('long_description', $content['longDescription'] ?? null)
                        ->setParameter('meta_description', $content['metaDescription'] ?? null)
                        ->setParameter('meta_keywords', $content['metaKeywords'] ?? null)
                        ->setParameter('meta_title', $content['metaTitle'] ?? null)
                        ->setParameter('short_description', $content['shortDescription'] ?? null)
                        ->setParameter('product_name', $content['productName'] ?? null)
                        ->setParameter('whats_included', $content['whatsIncluded'] ?? null)
                        ->setParameter('cover_image_id', isset($content['coverImageId']) ? Uuid::fromHexToBytes($content['coverImageId']) : null)
                        ->executeStatement();
                } else {
                    $contentId = Uuid::fromStringToHex($productId . $content['salesChannelId']);
                    //  Insert new content
                    $this->connection->createQueryBuilder()
                        ->insert('sales_channel_specific_content')
                        ->values([
                            'id' => ':id',
                            'product_id' => ':product_id',
                            'sales_channel_id' => ':sales_channel_id',
                            'long_description' => ':long_description',
                            'meta_description' => ':meta_description',
                            'meta_keywords' => ':meta_keywords',
                            'meta_title' => ':meta_title',
                            'short_description' => ':short_description',
                            'product_name' => ':product_name',
                            'whats_included' => ':whats_included',
                            'cover_image_id' => ':cover_image_id',
                            'created_at' => 'NOW()',
                            'updated_at' => 'NOW()',
                        ])
                        ->setParameter('id', $contentId)
                        ->setParameter('product_id', Uuid::fromHexToBytes($productId))
                        ->setParameter('sales_channel_id', Uuid::fromHexToBytes($content['salesChannelId']))
                        ->setParameter('long_description', $content['longDescription'] ?? null)
                        ->setParameter('meta_description', $content['metaDescription'] ?? null)
                        ->setParameter('meta_keywords', $content['metaKeywords'] ?? null)
                        ->setParameter('meta_title', $content['metaTitle'] ?? null)
                        ->setParameter('short_description', $content['shortDescription'] ?? null)
                        ->setParameter('product_name', $content['productName'] ?? null)
                        ->setParameter('whats_included', $content['whatsIncluded'] ?? null)
                        ->setParameter('cover_image_id', isset($content['coverImageId']) ? Uuid::fromHexToBytes($content['coverImageId']) : null)
                        ->executeStatement();
                }

                //  Handle Images (Check If Exists, Then Update or Insert)
                foreach ($salesChannelImages as $image) {
                    $imageId = Uuid::fromHexToBytes(Uuid::randomHex());

                    $imageExists = $this->connection->createQueryBuilder()
                        ->select('id')
                        ->from('sales_channel_specific_images')
                        ->where('sales_channel_content_id = :content_id')
                        ->andWhere('media_id = :media_id')
                        ->setParameter('content_id', $contentId)
                        ->setParameter('media_id', Uuid::fromHexToBytes($image['mediaId']))
                        ->executeQuery()
                        ->fetchOne();

                    if ($imageExists) {
                        //  Update existing image
                        $this->connection->createQueryBuilder()
                            ->update('sales_channel_specific_images')
                            ->set('position', ':position')
                            ->set('updated_at', 'NOW()')
                            ->where('sales_channel_content_id = :content_id')
                            ->andWhere('media_id = :media_id')
                            ->setParameter('content_id', $contentId)
                            ->setParameter('media_id', Uuid::fromHexToBytes($image['mediaId']))
                            ->setParameter('position', $image['position'] ?? 0)
                            ->executeStatement();
                    } else {
                        //  Insert new image
                        $this->connection->createQueryBuilder()
                            ->insert('sales_channel_specific_images')
                            ->values([
                                'id' => ':id',
                                'sales_channel_content_id' => ':content_id',
                                'media_id' => ':media_id',
                                'position' => ':position',
                                'created_at' => 'NOW()',
                                'updated_at' => 'NOW()',
                            ])
                            ->setParameter('id', $imageId)
                            ->setParameter('content_id', $contentId)
                            ->setParameter('media_id', Uuid::fromHexToBytes($image['mediaId']))
                            ->setParameter('position', $image['position'] ?? 0)
                            ->executeStatement();
                    }
                }
            }

            return new JsonResponse(['message' => 'Sales Channel Specific Content and Images saved successfully']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Database error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getContent(string $productId): JsonResponse
    {
        try {
            // Get Sales Channel Specific Content
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder
                ->select('id, product_id as productId, sales_channel_id as salesChannelId, long_description as longDescription, meta_description as metaDescription, meta_keywords as metaKeywords, meta_title as metaTitle, short_description as shortDescription,  product_name as productName, whats_included as whatsIncluded, cover_image_id as coverImageId')
                ->from('sales_channel_specific_content')
                ->where('product_id = :product_id')
                ->setParameter('product_id', Uuid::fromHexToBytes($productId));

            $contentData = $queryBuilder->executeQuery()->fetchAllAssociative();

            // Get Associated Images
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder
                ->select('id, sales_channel_content_id as SalesChannelContentId, media_id as mediaId, position')
                ->from('sales_channel_specific_images')
                ->where('sales_channel_content_id IN (
                SELECT id FROM sales_channel_specific_content WHERE product_id = :product_id
            )')
                ->setParameter('product_id', Uuid::fromHexToBytes($productId));

            $imageData = $queryBuilder->executeQuery()->fetchAllAssociative();

            // Convert to UTF-8 to fix JSON encoding issues
            $contentData = $this->convertToUtf8($contentData);
            $imageData = $this->convertToUtf8($imageData);
            foreach ($imageData as &$image) {
                $image['id'] = Uuid::fromStringToHex($image['id']);
                $image['SalesChannelContentId'] = Uuid::fromStringToHex($image['SalesChannelContentId']);
                $image['mediaId'] = Uuid::fromStringToHex($image['mediaId']);
            }
            foreach ($contentData as &$content) {
                $content['coverImageId'] = $content['coverImageId'] ? Uuid::fromStringToHex($content['coverImageId']) : null;
                $content['id'] = Uuid::fromStringToHex($content['id']);
                $content['productId'] = Uuid::fromStringToHex($content['productId']);
                $content['salesChannelId'] = Uuid::fromStringToHex($content['salesChannelId']);
                $content['salesChannelSpecificImages'] = array_filter($imageData, function ($image) use ($content) {
                    return $image['SalesChannelContentId'] === $content['id'];
                });

            }
            return new JsonResponse([
                'status' => 'success',
                'message' => 'Sales Channel Specific Content and Images fetched successfully',
                'data' => $contentData
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Database error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Converts an array of data to UTF-8 to avoid encoding issues.
     */
    private function convertToUtf8(array $data): array
    {
        return array_map(function ($item) {
            return array_map(function ($value) {
                return is_string($value) ? utf8_encode($value) : $value;
            }, $item);
        }, $data);
    }
}