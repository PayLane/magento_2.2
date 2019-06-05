<?php

declare(strict_types=1);

/**
 * File: GetData.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Controller\SecureForm;

use LizardMedia\PayLane\Api\Config\GeneralConfigProviderInterface;
use LizardMedia\PayLane\Api\Config\Methods\SecureFormConfigProviderInterface;
use LizardMedia\PayLane\Model\Config\Methods\SecureFormConfigProvider;
use LizardMedia\PayLane\Model\TransactionHandler;
use Magento\Checkout\Model\Session;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class GetData
 * @package LizardMedia\PayLane\Controller\SecureForm
 */
class GetData extends Action
{
    /**
     * @var SecureFormConfigProviderInterface
     */
    private $secureFormConfigProvider;

    /**
     * @var GeneralConfigProviderInterface
     */
    private $generalConfigProvider;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var array
     */
    private $allowedLanguages = [];

    /**
     * @param SecureFormConfigProviderInterface $secureFormConfigProvider
     * @param GeneralConfigProviderInterface $generalConfigProvider
     * @param Context $context
     * @param Session $session
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        SecureFormConfigProviderInterface $secureFormConfigProvider,
        GeneralConfigProviderInterface $generalConfigProvider,
        Context $context,
        Session $session,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->secureFormConfigProvider = $secureFormConfigProvider;
        $this->generalConfigProvider = $generalConfigProvider;
        $this->session = $session;
        $this->storeManager = $storeManager;
        $this->allowedLanguages = ['en', 'pl', 'de', 'es', 'fr', 'nl', 'it'];
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $data = [
            'action' => 'https://secure.paylane.com/order/cart.html',
            'method' => 'POST',
            'fields' => []
        ];

        $quote = $this->session->getQuote();

        if ($quote->getId()) {
            $payment = $quote->getPayment();
            $payment->setMethod(SecureFormConfigProvider::CODE_SECURE_FORM);
            $payment->save();

            if (!$quote->getCheckoutMethod()) {
                if (!$quote->getCustomer()->getEmail()) {
                    $quote->setCheckoutMethod(Onepage::METHOD_GUEST);
                } else {
                    $quote->setCheckoutMethod(Onepage::METHOD_REGISTER);
                }
            }

            $quote->reserveOrderId();
            $incrementId = $quote->getReservedOrderId();
            $quote->save();

            $result = [
                'amount' => sprintf('%01.2f', $quote->getGrandTotal()),
                'currency' => $quote->getQuoteCurrencyCode(),
                'merchant_id' => $this->generalConfigProvider->getMerchantId(),
                'description' => $incrementId,
                'transaction_description' => $this->_buildTransactionDescription($quote, $incrementId),
                'transaction_type' => TransactionHandler::TYPE_SALE,
                'back_url' => $this->_url->getUrl(
                    'paylane/secureForm/handle/quote/' . $quote->getId(),
                    ['_nosid' => true]
                ),
                'language' => $this->_getFormLanguage(
                    substr(
                        (string) $this->storeManager->getStore()->getLocaleCode(),
                        0, 2
                    )
                ),
            ];

            $result['hash'] = $this->_calculateHash(
                $result['description'],
                $result['amount'],
                $result['currency'],
                $result['transaction_type']
            );

            if ($this->secureFormConfigProvider->isSendCustomerData()) {
                $address = $quote->getBillingAddress();
                $result['customer_name'] = $quote->getCustomerFirstname() . ' ' . $quote->getCustomerLastname();
                $result['customer_email'] = $address->getEmail();
                $result['customer_address'] = implode(',', $address->getStreet(true));
                $result['customer_zip'] = $address->getPostcode();
                $result['customer_city'] = $address->getCity();
                $result['customer_state'] = $address->getRegion();
                $result['customer_country'] = $address->getCountry();
            }

            $data['fields'] = $result;
        }

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($data);
        return $resultJson;
    }

    /**
     * @param $description
     * @param $amount
     * @param $currency
     * @param $transactionType
     * @return string
     */
    protected function _calculateHash($description, $amount, $currency, $transactionType)
    {
        $salt = $this->generalConfigProvider->getHashSalt();
        $hash = sha1($salt . '|' . $description . '|' . $amount . '|' . $currency . '|' . $transactionType);

        return $hash;
    }

    /**
     * @param $quote
     * @param $orderId
     * @return \Magento\Framework\Phrase
     */
    protected function _buildTransactionDescription($quote, $orderId)
    {
        $resultString = __(
            'Order #%1, %2 (%3)',
            $orderId,
            $quote->getCustomerFirstname() . ' ' . $quote->getCustomerLastname(),
            $quote->getCustomerEmail()
        );

        return $resultString;
    }

    /**
     * @param $langCode
     * @return string
     */
    protected function _getFormLanguage($langCode)
    {
        return in_array($langCode, $this->allowedLanguages) ? $langCode : 'en';
    }
}
