<?php

declare(strict_types=1);

/**
 * File: Applepay.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Payment\Adapter;

use Exception;
use LizardMedia\PayLane\Api\Adapter\PayLaneRestClientFactoryInterface;
use LizardMedia\PayLane\Model\Config\GeneralConfigProvider;
use LizardMedia\PayLane\Model\Payment\Request\Builder\CustomerDataBuilder;
use LizardMedia\PayLane\Model\Payment\Request\Builder\SaleDataBuilder;
use LizardMedia\PayLane\Model\Payment\Request\Builder\TokenBuilder;
use LizardMedia\PayLane\Model\TransactionHandler;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Sales\Model\Order;

/**
 * Class Applepay
 * @package LizardMedia\PayLane\Model\Payment\Adapter
 */
class Applepay extends AbstractAdapter
{
    /**
     * @var array
     */
    protected $params;

    /**
     * @var GeneralConfigProvider
     */
    private $generalConfigProvider;

    /**
     * @var SaleDataBuilder
     */
    protected $saleBuilder;

    /**
     * @var TokenBuilder
     */
    protected $tokenBuilder;

    /**
     * @var CustomerDataBuilder
     */
    protected $customerDataBuilder;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var CartManagementInterface
     */
    protected $cartManagementInterface;

    /**
     * @var TransactionHandler
     */
    protected $transactionHandler;

    /**
     * Applepay constructor.
     * @param GeneralConfigProvider $generalConfigProvider
     * @param PayLaneRestClientFactoryInterface $payLaneRestClientFactory
     * @param SaleDataBuilder $saleBuilder
     * @param RedirectInterface $redirect
     * @param Order $order
     * @param CartManagementInterface $cartManagementInterface
     * @param TransactionHandler $transactionHandler
     * @param TokenBuilder $tokenBuilder
     * @param CustomerDataBuilder $customerDataBuilder
     */
    public function __construct(
        GeneralConfigProvider $generalConfigProvider,
        PayLaneRestClientFactoryInterface $payLaneRestClientFactory,
        SaleDataBuilder $saleBuilder,
        RedirectInterface $redirect,
        Order $order,
        CartManagementInterface $cartManagementInterface,
        TransactionHandler $transactionHandler,
        TokenBuilder $tokenBuilder,
        CustomerDataBuilder $customerDataBuilder
    ) {
        parent::__construct($payLaneRestClientFactory, $redirect);
        $this->generalConfigProvider = $generalConfigProvider;
        $this->saleBuilder = $saleBuilder;
        $this->tokenBuilder = $tokenBuilder;
        $this->order = $order;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->transactionHandler = $transactionHandler;
        $this->customerDataBuilder = $customerDataBuilder;
    }

    /**
     * @return array
     */
    public function getAdditionalFields()
    {
        return [
            'token'
        ];
    }

    /**
     * @param array $responseData
     * @param ResponseInterface $response
     * @return $this|mixed
     * @throws Exception
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function handleResponse(array $responseData, ResponseInterface $response)
    {
        $quote = $this->quote;
        $orderId = $this->cartManagementInterface->placeOrder($quote->getId());
        $order = $this->order->load($orderId);
        $orderStatus = $this->generalConfigProvider->getErrorOrderStatus();

        if ($order->getId()) {
            if ($responseData['success']) {
                $orderStatus = $this->generalConfigProvider->getClearedOrderStatus();
                $comment = __('Payment handled via PayLane module | Transaction ID: %1', $responseData['id_sale']);
                $orderPayment = $order->getPayment();
                $orderPayment->setTransactionId($responseData['id_sale']);
                $orderPayment->setIsTransactionClosed(true);
                $orderPayment->addTransaction('capture');
            } else {
                $error = $responseData['error'];
                $comment = __(
                    'Payment handled via PayLane module | Error (%1): %2',
                    $error['error_number'],
                    $error['error_description']
                );
            }
        
            $this->transactionHandler->setOrderState($order, $orderStatus, $comment);
            $order->save();
        }

        return $order;
    }

    /**
     * @param $params
     * @return void
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function handleRequest()
    {
        $responseData = $this->makeRequest($this->params);

        return $responseData;
    }

    /**
     * @return array|mixed
     */
    protected function buildRequest()
    {
        $result = [];
        return $result;
    }

    /**
     * @param array $requestData
     * @return mixed
     * @throws Exception
     */
    protected function makeRequest(array $requestData)
    {
        $client = $this->payLaneRestClientFactory->create();
        return $client->applePaySale($requestData);
    }
}
