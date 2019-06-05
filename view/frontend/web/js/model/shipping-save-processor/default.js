/**
 * File: default.js
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

define([
    'ko',
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/resource-url-manager',
    'mage/storage',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/model/payment/method-converter',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/select-billing-address'
], function (ko,
             $,
             quote,
             resourceUrlManager,
             storage,
             paymentService,
             methodConverter,
             errorProcessor,
             fullScreenLoader,
             selectBillingAddressAction) {
    'use strict';

    return {
        /**
         * @return {jQuery.Deferred}
         */
        saveShippingInformation: function () {
            var payload;
            var customerEmail = $("#customer-email").val();

            if (!quote.billingAddress()) {
                selectBillingAddressAction(quote.shippingAddress());
            }

            payload = {
                addressInformation: {
                    'shipping_address': quote.shippingAddress(),
                    'billing_address': quote.billingAddress(),
                    'shipping_method_code': quote.shippingMethod()['method_code'],
                    'shipping_carrier_code': quote.shippingMethod()['carrier_code']
                }
            };

            if (payload.addressInformation.billing_address) {
                payload.addressInformation.billing_address["email"] = customerEmail;
            }

            if (payload.addressInformation.shipping_address) {
                payload.addressInformation.shipping_address["email"] = customerEmail;
            }

            fullScreenLoader.startLoader();

            return storage.post(
                resourceUrlManager.getUrlForSetShippingInformation(quote),
                JSON.stringify(payload)
            ).done(
                function (response) {
                    quote.setTotals(response.totals);
                    paymentService.setPaymentMethods(methodConverter(response['payment_methods']));
                    fullScreenLoader.stopLoader();
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response);
                    fullScreenLoader.stopLoader();
                }
            );
        }
    };
});
