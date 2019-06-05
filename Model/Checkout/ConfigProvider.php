<?php

declare(strict_types=1);

/**
 * File: ConfigProvider.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Checkout;

use LizardMedia\PayLane\Api\Config\GeneralConfigProviderInterface;
use LizardMedia\PayLane\Api\Config\Methods\ApplePayConfigProviderInterface;
use LizardMedia\PayLane\Api\Config\Methods\BankTransferConfigProviderInterface;
use LizardMedia\PayLane\Api\Config\Methods\CreditCardConfigProviderInterface;
use LizardMedia\PayLane\Api\Config\Methods\DirectDebitConfigProviderInterface;
use LizardMedia\PayLane\Api\Config\Methods\IdealConfigProviderInterface;
use LizardMedia\PayLane\Api\Config\Methods\PayPalConfigProviderInterface;
use LizardMedia\PayLane\Api\Config\Methods\SecureFormConfigProviderInterface;
use LizardMedia\PayLane\Api\Config\Methods\SofortBankingConfigProviderInterface;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class ConfigProvider
 * @package LizardMedia\PayLane\Model\Checkout
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var ApplePayConfigProviderInterface
     */
    private $applePayConfigProvider;

    /**
     * @var BankTransferConfigProviderInterface
     */
    private $bankTransferConfigProvider;

    /**
     * @var CreditCardConfigProviderInterface
     */
    private $creditCardConfigProvider;

    /**
     * @var DirectDebitConfigProviderInterface
     */
    private $directDebitConfigProvider;

    /**
     * @var IdealConfigProviderInterface
     */
    private $idealConfigProvider;

    /**
     * @var PayPalConfigProviderInterface
     */
    private $payPalConfigProvider;

    /**
     * @var SecureFormConfigProviderInterface
     */
    private $secureFormConfigProvider;

    /**
     * @var SofortBankingConfigProviderInterface
     */
    private $sofortBankingConfigProvider;

    /**
     * @var GeneralConfigProviderInterface
     */
    private $generalConfigProvider;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * ConfigProvider constructor.
     * @param ApplePayConfigProviderInterface $applePayConfigProvider
     * @param BankTransferConfigProviderInterface $bankTransferConfigProvider
     * @param CreditCardConfigProviderInterface $creditCardConfigProvider
     * @param DirectDebitConfigProviderInterface $directDebitConfigProvider
     * @param IdealConfigProviderInterface $idealConfigProvider
     * @param PayPalConfigProviderInterface $payPalConfigProvider
     * @param SecureFormConfigProviderInterface $secureFormConfigProvider
     * @param SofortBankingConfigProviderInterface $sofortBankingConfigProvider
     * @param GeneralConfigProviderInterface $generalConfigProvider
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ApplePayConfigProviderInterface $applePayConfigProvider,
        BankTransferConfigProviderInterface $bankTransferConfigProvider,
        CreditCardConfigProviderInterface $creditCardConfigProvider,
        DirectDebitConfigProviderInterface $directDebitConfigProvider,
        IdealConfigProviderInterface $idealConfigProvider,
        PayPalConfigProviderInterface $payPalConfigProvider,
        SecureFormConfigProviderInterface $secureFormConfigProvider,
        SofortBankingConfigProviderInterface $sofortBankingConfigProvider,
        GeneralConfigProviderInterface $generalConfigProvider,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->applePayConfigProvider = $applePayConfigProvider;
        $this->bankTransferConfigProvider = $bankTransferConfigProvider;
        $this->creditCardConfigProvider = $creditCardConfigProvider;
        $this->directDebitConfigProvider = $directDebitConfigProvider;
        $this->idealConfigProvider = $idealConfigProvider;
        $this->payPalConfigProvider = $payPalConfigProvider;
        $this->secureFormConfigProvider = $secureFormConfigProvider;
        $this->sofortBankingConfigProvider = $sofortBankingConfigProvider;
        $this->generalConfigProvider = $generalConfigProvider;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        $months = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[] = sprintf("%02d", $i);
        }

        $year = date('Y');
        $years = [];

        for ($i = 0; $i < 11; $i++) {
            $years[$year] = $year;
            $year++;
        }

        $result = [
            'store' => [
                'name' => $this->scopeConfig->getValue('general/store_information/name')
            ],
            'payment' => [
                'ccform' => [
                    'months' => [
                        'paylane_creditcard' => $months
                    ],
                    'years' => [
                        'paylane_creditcard' => $years
                    ],
                    'hasVerification' => [
                        'paylane_creditcard' => true
                    ]
                ],
                'paylane' => [
                    'api_key' => $this->generalConfigProvider->getApiKey()
                ],
                'paylane_banktransfer' => [
                    'show_img' => $this->bankTransferConfigProvider->isPaymentMethodImageShown()
                ],
                'paylane_creditcard' => [
                    'show_img' => $this->creditCardConfigProvider->isPaymentMethodImageShown()
                ],
                'paylane_sofort' => [
                    'show_img' => $this->sofortBankingConfigProvider->isPaymentMethodImageShown()
                ],
                'paylane_directdebit' => [
                    'show_img' => $this->directDebitConfigProvider->isPaymentMethodImageShown()
                ],
                'paylane_paypal' => [
                    'show_img' => $this->payPalConfigProvider->isPaymentMethodImageShown()
                ],
                'paylane_ideal' => [
                    'show_img' => $this->idealConfigProvider->isPaymentMethodImageShown()
                ],
                'paylane_applepay' => [
                    'show_img' => $this->applePayConfigProvider->isPaymentMethodImageShown()
                ],
                'paylane_secureform' => [
                    'show_img' => $this->secureFormConfigProvider->isPaymentMethodImageShown()
                ]
            ]
        ];
        
        return $result;
    }
}
