import template from './sw-product-detail-content.html.twig';

const { Component, Mixin } = Shopware;

if (!Component.getComponentRegistry().has('sw-product-detail-content')) {
    Component.register('sw-product-detail-content', {
        template,
        inject: ['repositoryFactory'],

        mixins: [Mixin.getByName('notification')],

        props: {
            product: {
                type: Object,
                required: true
            }
        },

        data() {
            return {
                salesChannelSpecificContent: {
                    id: null,
                    longDescription: '',
                    metaDescription: '',
                    metaTitle: '',
                    metaKeywords: '',
                    productName: '',
                    productFeatures: "",
                    shortDescription: '',
                    productUrl: '',
                    whatsIncluded: '',
                    wholesalePrice: 0.00,
                    retailPrice: 0.00,
                    coverImageId: null,
                    salesChannelId: null,
                },
                salesChannelImages: [],
                selectedSalesChannel: null,
                salesChannelData: [],
                isLoading: false,
                salesChannelRepository: this.repositoryFactory.create('sales_channel'),
                productRepository: this.repositoryFactory.create('product'),
                salesChannelContentRepository: this.repositoryFactory.create('sales_channel_specific_content'),
                salesChannelImagesRepository: this.repositoryFactory.create('sales_channel_specific_images'),
                criteria: this.createCriteria(),
            };
        },

        watch: {
            salesChannelImages(newImages) {
                console.log("📸 Images Selected:", newImages);
            },
            selectedSalesChannel(newVal) {
                if (newVal) {
                    this.fetchSalesChannelContent(newVal);
                }
            }
        },

        methods: {
            getProductIdFromRoute() {
                return this.$route.params.id;
            },

            async fetchSalesChannelContent(salesChannelId = null) {
                const productId = this.getProductIdFromRoute();

                if (!productId) {
                    this.createNotificationError({
                        title: 'Error',
                        message: 'Product ID not found in URL!',
                    });
                    return;
                }

                this.isLoading = true;

                try {
                    const criteria = new Shopware.Data.Criteria();
                    criteria.addFilter(Shopware.Data.Criteria.equals('productId', productId));
                    criteria.addFilter(Shopware.Data.Criteria.equals('salesChannelId', salesChannelId || this.selectedSalesChannel));

                    const salesChannelContent = await this.salesChannelContentRepository.search(criteria, Shopware.Context.api);

                    if (salesChannelContent.length > 0) {
                        const filteredData = salesChannelContent[0]; 

                        this.salesChannelSpecificContent = {
                            ...filteredData,
                            productFeatures: typeof filteredData.productFeatures === 'string' ? filteredData.productFeatures : '',
                        };

                        const imageCriteria = new Shopware.Data.Criteria();
                        imageCriteria.addFilter(Shopware.Data.Criteria.equals('SalesChannelContentId', filteredData.id));

                        this.salesChannelImages = await this.salesChannelImagesRepository.search(imageCriteria, Shopware.Context.api);
                    } else {
                        this.salesChannelSpecificContent = this.getEmptyContent(salesChannelId);
                        this.salesChannelImages = [];
                    }
                } catch (error) {
                    this.createNotificationError({
                        title: 'Error',
                        message: 'Failed to fetch sales channel content.',
                    });
                } finally {
                    this.isLoading = false;
                }
            },

            async saveSalesChannelContent() {
                const productId = this.getProductIdFromRoute();

                if (!productId) {
                    this.createNotificationError({
                        title: 'Error',
                        message: 'Product ID not found in URL!',
                    });
                    return;
                }

                if (!this.selectedSalesChannel) {
                    this.createNotificationError({
                        title: 'Error',
                        message: 'Please select a Sales Channel before saving.',
                    });
                    return;
                }

                this.isLoading = true;

                try {
                    let entity;

                    if (this.salesChannelSpecificContent.id) {
                        entity = await this.salesChannelContentRepository.get(this.salesChannelSpecificContent.id, Shopware.Context.api);
                        Object.assign(entity, this.salesChannelSpecificContent);
                    } else {
                        entity = this.salesChannelContentRepository.create(Shopware.Context.api);
                        Object.assign(entity, {
                            ...this.salesChannelSpecificContent,
                            id: Shopware.Utils.createId(), 
                            productId, 
                            salesChannelId: this.selectedSalesChannel,
                        });
                    }

                    await this.salesChannelContentRepository.save(entity, Shopware.Context.api);

                    const savedEntity = await this.salesChannelContentRepository.get(entity.id, Shopware.Context.api);
                    this.salesChannelSpecificContent = savedEntity;

                    console.log("✅ Images to be saved:", this.salesChannelImages);

                    await Promise.all(
                        this.salesChannelImages.map(async (image, index) => {
                            let imageEntity;

                            if (!image.mediaId) {
                                console.error("❌ Missing mediaId for image:", image);
                                return;
                            }

                            if (image.id) {
                                console.log("🔄 Updating existing image:", image);
                                imageEntity = await this.salesChannelImagesRepository.get(image.id, Shopware.Context.api);
                                Object.assign(imageEntity, {
                                    mediaId: image.mediaId,
                                    SalesChannelContentId: entity.id,
                                    position: index
                                });
                            } else {
                                console.log("➕ Creating new image:", image);
                                imageEntity = this.salesChannelImagesRepository.create(Shopware.Context.api);
                                Object.assign(imageEntity, {
                                    mediaId: image.mediaId,
                                    SalesChannelContentId: entity.id,
                                    position: index
                                });
                            }

                            await this.salesChannelImagesRepository.save(imageEntity, Shopware.Context.api);
                        })
                    );

                    this.createNotificationSuccess({
                        title: 'Success',
                        message: 'Sales channel content and images saved successfully!',
                    });

                } catch (error) {
                    console.error('❌ Error saving sales channel content:', error);
                    this.createNotificationError({
                        title: 'Error',
                        message: 'Failed to save sales channel content.',
                    });
                } finally {
                    this.isLoading = false;
                }
            },

            updateSalesChannelContent() {
                const selectedContent = this.salesChannelData.find(
                    item => String(item.salesChannelId) === String(this.selectedSalesChannel)
                );

                if (selectedContent) {
                    this.salesChannelSpecificContent = {
                        ...selectedContent,
                        productFeatures: typeof filteredData.productFeatures === 'string' ? filteredData.productFeatures : '',
                    };
                } else {
                    this.salesChannelSpecificContent = this.getEmptyContent(this.selectedSalesChannel);
                }
            },

            createCriteria() {
                const criteria = new Shopware.Data.Criteria();
                criteria.addFilter(Shopware.Data.Criteria.equals('active', true));
                criteria.addSorting(Shopware.Data.Criteria.sort('name', 'ASC'));
                return criteria;
            },

            onSalesChannelChange(salesChannelId) {
                console.log("📌 Sales Channel Selected:", salesChannelId);

                this.selectedSalesChannel = salesChannelId;
                this.salesChannelSpecificContent.salesChannelId = salesChannelId;
                this.salesChannelSpecificContent.id = salesChannelId;

                if (salesChannelId) {
                    console.log("Fetching content for Sales Channel ID:", salesChannelId);
                    this.fetchSalesChannelContent(salesChannelId);
                } else {
                    this.salesChannelSpecificContent = {};
                }
            },

            getEmptyContent(salesChannelId = null) {
                return {
                    id: null,
                    productId: null,
                    salesChannelId,
                    longDescription: '',
                    metaDescription: '',
                    metaTitle: '',
                    metaKeywords: '',
                    shortDescription: '',
                    productName: '',
                    productFeatures: '',
                    whatsIncluded: '',
                    wholesalePrice: 0.00,
                    retailPrice: 0.00,
                    coverImageId: null,
                    salesChannelSpecificImages: [],
                };
            }
        },

        created() {
            this.fetchSalesChannelContent();
        }
    });
}
