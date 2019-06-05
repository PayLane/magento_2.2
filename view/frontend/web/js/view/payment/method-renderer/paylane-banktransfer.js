/**
 * File: paylane-banktransfer.js
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

define(
    [
        'jquery',
        'underscore',
        'mage/template',
        'mage/url',
        'mage/translate',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Customer/js/customer-data'
    ],
    function ($,
              _,
              mageTemplate,
              url,
              $t,
              Component,
              fullScreenLoader,
              customerData) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'LizardMedia_PayLane/payment/banktransfer',
                code: 'paylane_banktransfer',
                additional_data: {
                    'payment_type': ''
                }
            },

            isActive: function () {
                var active = (this.getCode() === this.isChecked());
                return active;
            },

            /**
             * Get data
             * @returns {Object}
             */
            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'payment_type': this.additional_data.payment_type
                    }
                };
            },

            getBankImgUrl: function (name) {
                return window.paylaneImgPath + '/banks/' + name + '.png';
            },

            showPaymentMethodImg: function () {
                return window.checkoutConfig.payment.paylane_banktransfer.show_img;
            },

            getPaymentMethodImgUrl: function () {
                return window.paylaneImgPath + '/payment_methods/banktransfer.png';
            },

            setPaymentType: function (name) {
                this.additional_data.payment_type = name;
            },

            getPaymentTypes: function () {
                return [
                    {value: 'AB', label: 'Alior Bank'},
                    {value: 'AS', label: 'T-Mobile Usługi Bankowe'},
                    {value: 'MT', label: 'mTransfer'},
                    {value: 'IN', label: 'Inteligo'},
                    {value: 'IP', label: 'iPKO'},
                    {value: 'MI', label: 'Millenium'},
                    {value: 'CA', label: 'Credit Agricole'},
                    {value: 'PP', label: 'Poczta Polska'},
                    {value: 'PCZ', label: 'Bank Pocztowy'},
                    {value: 'IB', label: 'Idea Bank'},
                    {value: 'PO', label: 'Pekao S.A.'},
                    {value: 'GB', label: 'Getin Bank'},
                    {value: 'IG', label: 'ING Bank Śląski'},
                    {value: 'WB', label: 'Bank Zachodni WBK'},
                    {value: 'PB', label: 'Bank BGŻ BNP PARIBAS'},
                    {value: 'CT', label: 'Citi'},
                    {value: 'PL', label: 'PlusBank'},
                    {value: 'NP', label: 'Noble Pay'},
                    {value: 'BS', label: 'Bank Spółdzielczy'},
                    {value: 'NB', label: 'NestBank'},
                    {value: 'PBS', label: 'Podkarpacki Bank Spółdzielczy'},
                    {value: 'SGB', label: 'Spółdzielcza Grupa Bankowa'},
                    {value: 'DB', label: 'Deutsche Bank'},
                    {value: 'BP', label: 'Bank BPH'},
                    {value: 'OH', label: $t('Other bank')}
                ];
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