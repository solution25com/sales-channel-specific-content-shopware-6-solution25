import './module/multichannel-content'


Shopware.Module.register('sw-product-detail-content-tab', {
    routeMiddleware(next, currentRoute) {
        const customRouteName = 'sw.product.detail.content'; 

        if(currentRoute.name === 'sw.product.detail' && currentRoute.children.every((currentRoute) => currentRoute.name !== customRouteName)){
            currentRoute.children.push({
                name: 'sw.product.detail.content',
                path: '/sw/product/detail/:id/content',
                component: 'sw-product-detail-content',
                meta: {
                    parentPath: 'sw.product.index',
                }
            });
        }
        next(currentRoute);
    }
});