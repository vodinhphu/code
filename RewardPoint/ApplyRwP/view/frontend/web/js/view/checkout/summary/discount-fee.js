define(
    [
        'jquery',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals',
        'Magento_Catalog/js/price-utils'
    ],
    function ($, Component, quote, totals, priceUtils) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'RewardPoint_ApplyRwP/checkout/summary/discount-fee'
            },
            totals: quote.getTotals(),
            isDisplayedDiscountTotal: function () {
                return true;
            },
            getDiscountTotal: function () {
                var price = totals.getSegment('discount').value;
                return this.getFormattedPrice(price);
            }
        });
    }
);
