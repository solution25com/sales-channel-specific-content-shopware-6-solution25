import template from './sw-product-detail.html.twig';

const { Component, Mixin } = Shopware;

Component.override('sw-product-detail', {
    template,

    metaInfo() {
        return {
            title: 'Custom'
        };
    }
});