/**
 * File: paylane-applepay.js
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

define(
    [
        'mage/url',
        'mage/translate',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Customer/js/customer-data',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/quote',
        'mage/storage',
        'Magento_Customer/js/model/customer',
    ],
    function (
        url,
        $t,
        Component,
        fullScreenLoader,
        customerData,
        errorProcessor,
        quote,
        storage,
        customer) {
        'use strict'
        return Component.extend({
            defaults: {
                template: 'LizardMedia_PayLane/payment/applepay',
                code: 'paylane_applepay',
                applePaySession: null,
                applePayActive: true,
                additional_data: {
                    'token': '',
                },
            },

            isActive: function () {
                var active = (this.getCode() === this.isChecked())
                return active
            },

            initialize: function () {
                this._super()
                PayLane.setPublicApiKey(window.checkoutConfig.payment.paylane.api_key)
                PayLane.applePay.checkAvailability(function (available) {
                    if (!available) {
                        this.applePayActive = false
                        return console.warn('Apple Pay not available')
                    }
                    this.applePayActive = true
                }.bind(this))

            },

            /**
             * Get data
             * @returns {Object}
             */
            getData: function () {
                return {
                    'method': this.item.method,
                    'applePaySession': this.applePaySession,
                    'applePayActive': this.applePayActive,
                    'additional_data': {
                        'token': this.additional_data.token,
                    },
                }
            },

            showPaymentMethodImg: function () {
                return window.checkoutConfig.payment.paylane_applepay.show_img;
            },

            getPaymentMethodImgUrl: function () {
                return window.paylaneImgPath + '/payment_methods/applepay.png'
            },

            getCartParams: function () {
                var checkoutConfig = window.checkoutConfig
                var storeName = checkoutConfig.store.name ? checkoutConfig.store.name : url.build('')
                var totals = quote.totals()
                var paymentRequest = {
                    countryCode: checkoutConfig.defaultCountryId,
                    currencyCode: totals.base_currency_code,
                    total: {
                        label: $t('Order from shop ') + storeName,
                        amount: parseFloat(totals.base_grand_total).toFixed(2),
                    },
                }

                return paymentRequest
            },

            onApplePayAuthorized: function (paymentResult, completion) {
                var _self = this

                if (customer.isLoggedIn()) {
                    paymentResult.quote_id = quote.getQuoteId()
                    customerData.invalidate(['cart'])

                    var data = JSON.stringify(paymentResult)
                    var headers = {
                        'user-agent': 'Mozilla/4.0 MDN Example',
                        'content-type': 'application/json',
                    }
                    var fetchData = {
                        method: 'POST',
                        headers: headers,
                        body: data,
                    }

                    fetch(url.build('paylane/applepay/handle'), fetchData).then(function (result) {
                        return result.json()
                    }).then(function (result) {
                        if (result.success) {
                            completion(ApplePaySession.STATUS_SUCCESS)

                            setTimeout(function () {
                                var link = url.build(
                                    'paylane/applepay/redirect?order_id=' + result.order_id + '&order_status=' +
                                    result.order_status + '&increment_id=' + result.increment_id + '&quote_id=' +
                                    result.quote_id)
                                window.location.href = link
                            }, 3000)
                        }
                        else {
                            _self.onError(result.error_description)
                            completion(ApplePaySession.STATUS_FAILURE)
                            fullScreenLoader.stopLoader()
                        }
                    })
                }
                else {
                    storage.get(
                        url.build('rest/V1/guest-carts/' + quote.getQuoteId())
                    ).done(
                        function (response) {
                            paymentResult.quote_id = response.id
                            customerData.invalidate(['cart'])

                            var data = JSON.stringify(paymentResult)
                            var headers = {
                                'user-agent': 'Mozilla/4.0 MDN Example',
                                'content-type': 'application/json',
                            }
                            var fetchData = {
                                method: 'POST',
                                headers: headers,
                                body: data,
                            }

                            fetch(url.build('paylane/applepay/handle'), fetchData).then(function (result) {
                                return result.json()
                            }).then(function (result) {
                                if (result.message) {
                                    alert(JSON.stringify(result.message))
                                }

                                if (result.success) {
                                    completion(ApplePaySession.STATUS_SUCCESS)

                                    setTimeout(function () {
                                        var link = url.build(
                                            'paylane/applepay/redirect?order_id=' + result.order_id + '&order_status=' +
                                            result.order_status + '&increment_id=' + result.increment_id +
                                            '&quote_id=' + result.quote_id)
                                        window.location.href = link
                                    }, 3000)
                                }
                                else {
                                    _self.onError(result.error_description)
                                    completion(ApplePaySession.STATUS_FAILURE)
                                    fullScreenLoader.stopLoader()
                                }
                            })
                        }
                    ).fail(
                        function (response) {
                            errorProcessor.process(response)
                            fullScreenLoader.stopLoader()
                            completion(ApplePaySession.STATUS_FAILURE)
                        }
                    )
                }
            },

            onError: function (result) {
                var _self = this
                errorProcessor.process({
                    responseText: JSON.stringify({
                        message: result,
                    }),
                }, _self.messageContainer)
                fullScreenLoader.stopLoader()
            },

            placeOrder: function () {
                fullScreenLoader.startLoader()
                this.applePaySession = PayLane.applePay.createSession(
                    this.getCartParams(),
                    this.onApplePayAuthorized,
                    this.onError
                )
            },
        })
    }
)