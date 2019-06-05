# PayLane for Magento 2 #
This module handles Magento 2.X integration for PayLane payment provider.

### Prerequisites ###

##### For release 1.0 #####
* Magento >= 2.0
* PHP >= 7.0

### Installing ###

#### Download the module ####

Download the module and unpack it into your project into```app/code/LizardMedia/PayLane```

#### Install the module ####

Run this command
```
bin/magento module:enable LizardMedia_PayLane
bin/magento setup:upgrade
```

#### Configure the module ####

Go to the backend of your Magento installation and visit:
```
Stores -> Configuration -> Sales -> Payment Methods
```
to configure the module.

##### API mode  #####
To use the API mode of the module fill the following fields:
* **Username** - the **API Login** field for your PayLane account
* **Password** - the **API Password** field for your PayLane account
* **API Key** - the **Public API Key** field for your PayLane account

![Alt text](docs/configuration.png?raw=true "Module configuration section")


##### Screenshots #####

![Alt text](docs/checkout.png?raw=true "Checkout - Review & Payments step")
