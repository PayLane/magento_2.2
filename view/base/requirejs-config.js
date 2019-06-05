/**
 * File: requirejs-config.js
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

var config = {
    map: {
       "*": {
          'Magento_Checkout/js/model/shipping-save-processor/default': 
              'LizardMedia_PayLane/js/model/shipping-save-processor/default'
       }
    }
 };