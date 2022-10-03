define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'bnpl_gateway',
                component: 'Apexx_Bnpl/js/view/payment/method-renderer/bnpl_gateway'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
