/**
 * File: paylane-creditcard.js
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

define(
    [
        'jquery',
        'underscore',
        'mage/template',
        'mage/url',
        'Magento_Payment/js/view/payment/cc-form',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Customer/js/customer-data',
        'Magento_Checkout/js/model/error-processor'
    ],
    function ($,
              _,
              mageTemplate,
              url,
              Component,
              fullScreenLoader,
              customerData,
              errorProcessor) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'LizardMedia_PayLane/payment/creditcard',
                code: 'paylane_creditcard',
                creditCardHolderName: '',
                additionalData: {}
            },

            initialize: function () {
                this._super();
                PayLane.setPublicApiKey(window.checkoutConfig.payment.paylane.api_key);
            },

            /**
             * Get data
             * @returns {Object}
             */
            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'cc_cid': this.creditCardVerificationNumber(),
                        'cc_ss_start_month': this.creditCardSsStartMonth(),
                        'cc_ss_start_year': this.creditCardSsStartYear(),
                        'cc_ss_issue': this.creditCardSsIssue(),
                        'cc_type': this.creditCardType(),
                        'cc_exp_year': this.creditCardExpYear(),
                        'cc_exp_month': this.creditCardExpMonth(),
                        'cc_number': this.creditCardNumber(),
                        'cc_holder_name': this.creditCardHolderName
                    }
                };
            },

            showPaymentMethodImg: function () {
                return window.checkoutConfig.payment.paylane_creditcard.show_img;
            },

            getPaymentMethodImgUrl: function () {
                return window.paylaneImgPath + '/payment_methods/creditcard.png';
            },

            /**
             * Get payment name
             *
             * @returns {String}
             */
            getCode: function () {
                return this.code;
            },

            isActive: function () {
                var active = (this.getCode() === this.isChecked());
                return active;
            },

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

            handleCheckoutError: function (message) {
                var _self = this;
                errorProcessor.process({
                    responseText: JSON.stringify({
                        message: message
                    })
                }, _self.messageContainer);
                fullScreenLoader.stopLoader();
            },

            placeOrder: function () {
                var _self = this;
                fullScreenLoader.startLoader();
                customerData.invalidate(['cart']);

                var data = _self.getData();

                var formData = {
                    method_code: data.method
                };

                var requestData = {
                    cardNumber: _self.creditCardNumber(),
                    expirationMonth: (_self.creditCardExpMonth() > 0 && _self.creditCardExpMonth() < 10) ? '0' + _self.creditCardExpMonth() : _self.creditCardExpMonth(),
                    expirationYear: _self.creditCardExpYear(),
                    nameOnCard: _self.creditCardHolderName,
                    cardSecurityCode: _self.creditCardVerificationNumber()
                };

                try {
                    PayLane.card.generateToken(requestData,
                        function (token) {
                            formData['additional_data[token]'] = token;
                            _self.build({
                                action: url.build('paylane/transaction/start'),
                                method: 'POST',
                                fields: formData
                            }).submit();
                        },
                        function (code, result) {
                            _self.handleCheckoutError(result);
                        });
                } catch (error) {
                    _self.handleCheckoutError(error.message);
                }
            }
        });
    }
);