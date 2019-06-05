/**
 * File: paylane-secureform.js
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

define(
    [
        'jquery',
        'underscore',
        'mage/template',
        'mage/url',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/customer-data'
    ],
    function ($,
              _,
              mageTemplate,
              url,
              Component,
              fullScreenLoader,
              errorProcessor,
              customerData) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'LizardMedia_PayLane/payment/secureform'
            },
            redirectAfterPlaceOrder: false,

            /**
             * @param {Object} formData
             * @returns {*|jQuery}
             */
            build: function (formData) {
                var formTmpl = mageTemplate('<form action="<%= data.action %>"' +
                    ' method="<%= data.method %>" hidden enctype="application/x-www-form-urlencoded">' +
                    '<% _.each(data.fields, function(val, key){ %>' +
                    '<input value=\'<%= val %>\' name="<%= key %>" type="hidden">' +
                    '<% }); %>' +
                    '</form>');

                return $(formTmpl({
                    data: {
                        action: formData.action,
                        method: formData.method,
                        fields: formData.fields
                    }
                })).appendTo($('[data-container="body"]'));
            },

            showPaymentMethodImg: function () {
                return window.checkoutConfig.payment.paylane_secureform.show_img;
            },

            getPaymentMethodImgUrl: function () {
                return window.paylaneImgPath + '/payment_methods/secureform.png';
            },

            placeOrder: function () {
                var self = this;
                fullScreenLoader.startLoader();
                $.get(url.build('paylane/secureForm/getData'))
                    .done(function (response) {
                        customerData.invalidate(['cart']);
                        self.build(response).submit();
                    }).fail(function (response) {
                    errorProcessor.process(response, self.messageContainer);
                }).always(function () {
                    fullScreenLoader.stopLoader();
                });
            }
        });
    }
);