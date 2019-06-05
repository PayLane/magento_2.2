/**
 * File: paylane.js
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component,
              rendererList) {
        'use strict';

        rendererList.push(
            {
                type: 'paylane_secureform',
                component: 'LizardMedia_PayLane/js/view/payment/method-renderer/paylane-secureform'
            }
        );

        rendererList.push(
            {
                type: 'paylane_creditcard',
                component: 'LizardMedia_PayLane/js/view/payment/method-renderer/paylane-creditcard'
            }
        );

        rendererList.push(
            {
                type: 'paylane_paypal',
                component: 'LizardMedia_PayLane/js/view/payment/method-renderer/paylane-paypal'
            }
        );

        rendererList.push(
            {
                type: 'paylane_sofort',
                component: 'LizardMedia_PayLane/js/view/payment/method-renderer/paylane-sofort'
            }
        );

        rendererList.push(
            {
                type: 'paylane_directdebit',
                component: 'LizardMedia_PayLane/js/view/payment/method-renderer/paylane-directdebit'
            }
        );

        rendererList.push(
            {
                type: 'paylane_banktransfer',
                component: 'LizardMedia_PayLane/js/view/payment/method-renderer/paylane-banktransfer'
            }
        );

        rendererList.push(
            {
                type: 'paylane_ideal',
                component: 'LizardMedia_PayLane/js/view/payment/method-renderer/paylane-ideal'
            }
        );

        rendererList.push(
            {
                type: 'paylane_applepay',
                component: 'LizardMedia_PayLane/js/view/payment/method-renderer/paylane-applepay'
            }
        );

        return Component.extend({});
    }
);