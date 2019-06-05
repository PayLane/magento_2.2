/**
 * File: paylane-directdebit.js
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
        'Magento_Customer/js/customer-data'
    ],
    function ($,
              _,
              mageTemplate,
              url,
              Component,
              fullScreenLoader,
              customerData) {
        'use strict';

        var countryData = customerData.get('directory-data');

        return Component.extend({
            defaults: {
                template: 'LizardMedia_PayLane/payment/directdebit',
                code: 'paylane_directdebit',
                additional_data: {
                    account_holder: '',
                    account_country: '',
                    iban: '',
                    bic: ''
                }
            },

            isActive: function () {
                var active = (this.getCode() === this.isChecked());
                return active;
            },

            getCountryValues: function () {
                var countries = countryData();
                var result = [];

                for (var countryCode in countries) {
                    if (countryCode !== 'data_id') {
                        result.push({
                            value: countryCode,
                            country: countries[countryCode].name
                        });
                    }
                }

                result.sort(function (a, b) {
                    return a.country.localeCompare(b.country);
                });

                return result;
            },

            showPaymentMethodImg: function () {
                return window.checkoutConfig.payment.paylane_directdebit.show_img;
            },

            getPaymentMethodImgUrl: function () {
                return window.paylaneImgPath + '/payment_methods/sepa.png';
            },

            /**
             * Get data
             * @returns {Object}
             */
            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'account_holder': this.additional_data.account_holder,
                        'account_country': this.additional_data.account_country,
                        'iban': this.additional_data.iban,
                        'bic': this.additional_data.bic
                    }
                };
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

            placeOrder: function () {
                var _self = this;
                fullScreenLoader.startLoader();
                customerData.invalidate(['cart']);

                var formData = {
                    method_code: this.item.method,
                    po_number: null,
                    additional_data: null
                };

                _.each(_self.getData().additional_data, function (val, key) {
                    formData['additional_data[' + key + ']'] = val;
                });

                _self.build({
                    action: url.build('paylane/transaction/start'),
                    method: 'POST',
                    fields: formData
                }).submit();
            }
        });
    }
);