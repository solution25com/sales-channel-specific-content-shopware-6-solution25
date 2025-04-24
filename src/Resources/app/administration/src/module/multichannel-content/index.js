import './page/sw-product-detail';
import './view/sw-product-detail-content';

const { Module } = Shopware;

Module.register('multichannel-content', {
    type: 'plugin',
    name: 'multichannel-content',
    title: 'multichannel-content.general.title',
    description: 'multichannel-content.general.description',

    routes: {
        list: {
            component: 'sw-product-detail',
            path: 'list',
        },
    },


});