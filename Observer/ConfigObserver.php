<?php

declare(strict_types=1);

/**
 * File: ConfigObserver.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Observer;

use LizardMedia\PayLane\Model\Config\GeneralConfigProvider;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class ConfigObserver
 * @package LizardMedia\PayLane\Observer
 */
class ConfigObserver implements ObserverInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $config;

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @var TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * ConfigObserver constructor.
     * @param ScopeConfigInterface $config
     * @param WriterInterface $configWriter
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(
        ScopeConfigInterface $config,
        WriterInterface $configWriter,
        TypeListInterface $cacheTypeList
    ) {
        $this->config = $config;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (
            $this->getConfigValue(GeneralConfigProvider::XML_PATH_PAYMENT_PAYLANE_PAYMENT_MODE) === 'SECURE_FORM'
        ) {
            $this->setConfigValue('payment/paylane_creditcard/active', 0);
            $this->setConfigValue('payment/paylane_banktransfer/active', 0);
            $this->setConfigValue('payment/paylane_paypal/active', 0);
            $this->setConfigValue('payment/paylane_directdebit/active', 0);
            $this->setConfigValue('payment/paylane_sofort/active', 0);
            $this->setConfigValue('payment/paylane_ideal/active', 0);
            $this->setConfigValue('payment/paylane_applepay/active', 0);
            $this->setConfigValue('payment/paylane_secureform/active', 1);
        }

        if ($this->getConfigValue(GeneralConfigProvider::XML_PATH_PAYMENT_PAYLANE_PAYMENT_MODE) === 'API') {
            $this->setConfigValue('payment/paylane_secureform/active', 0);
        }

        $this->cacheTypeList->cleanType('config');
    }


    /**
     * @param $path
     * @return mixed
     */
    protected function getConfigValue($path)
    {
        return $this->config->getValue($path, ScopeInterface::SCOPE_STORES);
    }

    /**
     * @param $path
     * @param $value
     * @return void
     */
    protected function setConfigValue($path, $value)
    {
        $this->configWriter->save($path, $value);
    }
}
