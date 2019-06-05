<?php

declare(strict_types=1);

/**
 * File: Banktransfer.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Payment\Adapter;

use Exception;
use LizardMedia\PayLane\Api\Adapter\PayLaneRestClientFactoryInterface;
use LizardMedia\PayLane\Api\Config\GeneralConfigProviderInterface;
use LizardMedia\PayLane\Model\Payment\Request\Builder\BackUrlBuilder;
use LizardMedia\PayLane\Model\Payment\Request\Builder\CustomerDataBuilder;
use LizardMedia\PayLane\Model\Payment\Request\Builder\PaymentTypeBuilder;
use LizardMedia\PayLane\Model\Payment\Request\Builder\SaleDataBuilder;
use LizardMedia\PayLane\Model\TransactionHandler;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Sales\Model\Order;

/**
 * Class Banktransfer
 * @package LizardMedia\PayLane\Model\Payment\Adapter
 */
class Banktransfer extends AbstractAdapter
{
    /**
     * @var GeneralConfigProviderInterface
     */
    protected $generalConfigProvider;

    /**
     * @var SaleDataBuilder
     */
    protected $saleBuilder;

    /**
     * @var BackUrlBuilder
     */
    protected $backUrlBuilder;

    /**
     * @var CustomerDataBuilder
     */
    protected $customerDataBuilder;

    /**
     * @var PaymentTypeBuilder
     */
    protected $paymentTypeBuilder;

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
     * Banktransfer constructor.
     * @param GeneralConfigProviderInterface $generalConfigProvider
     * @param PayLaneRestClientFactoryInterface $payLaneRestClientFactory
     * @param SaleDataBuilder $saleBuilder
     * @param RedirectInterface $redirect
     * @param Order $order
     * @param CartManagementInterface $cartManagementInterface
     * @param TransactionHandler $transactionHandler
     * @param BackUrlBuilder $backUrlBuilder
     * @param CustomerDataBuilder $customerDataBuilder
     * @param PaymentTypeBuilder $paymentTypeBuilder
     */
    public function __construct(
        GeneralConfigProviderInterface $generalConfigProvider,
        PayLaneRestClientFactoryInterface $payLaneRestClientFactory,
        SaleDataBuilder $saleBuilder,
        RedirectInterface $redirect,
        Order $order,
        CartManagementInterface $cartManagementInterface,
        TransactionHandler $transactionHandler,
        BackUrlBuilder $backUrlBuilder,
        CustomerDataBuilder $customerDataBuilder,
        PaymentTypeBuilder $paymentTypeBuilder
    ) {
        parent::__construct($payLaneRestClientFactory, $redirect);
        $this->generalConfigProvider = $generalConfigProvider;
        $this->payLaneRestClientFactory = $payLaneRestClientFactory;
        $this->saleBuilder = $saleBuilder;
        $this->backUrlBuilder = $backUrlBuilder;
        $this->order = $order;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->transactionHandler = $transactionHandler;
        $this->customerDataBuilder = $customerDataBuilder;
        $this->paymentTypeBuilder = $paymentTypeBuilder;
    }

    /**
     * @return array
     */
    public function getAdditionalFields(): array
    {
        return [
            'payment_type'
        ];
    }

    /**
     * @param array $responseData
     * @param ResponseInterface $response
     * @return mixed|void
     * @throws Exception
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function handleResponse(array $responseData, ResponseInterface $response)
    {
        $success = false;
        $orderStatus = $this->generalConfigProvider->getErrorOrderStatus();

        if (!empty($responseData['success']) && $responseData['success']) {
            header('Location: ' . $responseData['redirect_url']);
            die;
        } else {
            $quote = $this->quote;
            $orderId = $this->cartManagementInterface->placeOrder($quote->getId());
            $order = $this->order->load($orderId);
            $error = $responseData['error'];

            $comment = __(
                'Payment handled via PayLane module | Error (%1): %2',
                $error['error_number'],
                $error['error_description']
            );

            $this->transactionHandler->setOrderState($order, $orderStatus, $comment);
            $order->save();
        }

        $this->handleRedirect($success, $response);
    }

    /**
     * @return array|mixed
     */
    protected function buildRequest()
    {
        $result = [];
        $result = array_merge_recursive($result, $this->saleBuilder->build($this->quote));
        $result = array_merge_recursive($result, $this->backUrlBuilder->build($this->quote));
        $result = array_merge_recursive($result, $this->customerDataBuilder->build($this->quote));
        $result = array_merge_recursive($result, $this->paymentTypeBuilder->build($this->quote));

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
        return $client->bankTransferSale($requestData);
    }
}
