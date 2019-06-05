<?php

declare(strict_types=1);

/**
 * File: Directdebit.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Payment\Adapter;

use Exception;
use LizardMedia\PayLane\Api\Adapter\PayLaneRestClientFactoryInterface;
use LizardMedia\PayLane\Api\Config\GeneralConfigProviderInterface;
use LizardMedia\PayLane\Model\Payment\Request\Builder\AccountBuilder;
use LizardMedia\PayLane\Model\Payment\Request\Builder\CustomerDataBuilder;
use LizardMedia\PayLane\Model\Payment\Request\Builder\SaleDataBuilder;
use LizardMedia\PayLane\Model\TransactionHandler;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Sales\Model\Order;

/**
 * Class Directdebit
 * @package LizardMedia\PayLane\Model\Payment\Adapter
 */
class Directdebit extends AbstractAdapter
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
     * @var AccountBuilder
     */
    protected $accountBuilder;

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
     * Directdebit constructor.
     *
     * @param GeneralConfigProviderInterface $generalConfigProvider
     * @param PayLaneRestClientFactoryInterface $payLaneRestClientFactory
     * @param SaleDataBuilder $saleBuilder
     * @param RedirectInterface $redirect
     * @param Order $order
     * @param CartManagementInterface $cartManagementInterface
     * @param TransactionHandler $transactionHandler
     * @param AccountBuilder $accountBuilder
     * @param CustomerDataBuilder $customerDataBuilder
     */
    public function __construct(
        GeneralConfigProviderInterface $generalConfigProvider,
        PayLaneRestClientFactoryInterface $payLaneRestClientFactory,
        SaleDataBuilder $saleBuilder,
        RedirectInterface $redirect,
        Order $order,
        CartManagementInterface $cartManagementInterface,
        TransactionHandler $transactionHandler,
        AccountBuilder $accountBuilder,
        CustomerDataBuilder $customerDataBuilder
    ) {
        parent::__construct($payLaneRestClientFactory, $redirect);
        $this->generalConfigProvider = $generalConfigProvider;
        $this->saleBuilder = $saleBuilder;
        $this->accountBuilder = $accountBuilder;
        $this->order = $order;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->transactionHandler = $transactionHandler;
        $this->customerDataBuilder = $customerDataBuilder;
    }

    /**
     * @return array
     */
    public function getAdditionalFields(): array
    {
        return [
            'account_holder',
            'account_country',
            'iban',
            'bic'
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
        $quote = $this->quote;
        $orderId = $this->cartManagementInterface->placeOrder($quote->getId());
        $order = $this->order->load($orderId);
        $orderStatus = $this->generalConfigProvider->getErrorOrderStatus();

        if ($order->getId()) {
            if (!empty($responseData['success']) && $responseData['success']) {
                $orderStatus = $this->generalConfigProvider->getPendingOrderStatus();
                $success = true;
                $comment = __('Payment handled via PayLane module | Transaction ID: %1', $responseData['id_sale']);
                $orderPayment = $order->getPayment();
                $orderPayment->setTransactionId($responseData['id_sale']);
                $orderPayment->setIsTransactionClosed(false);
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

        $this->handleRedirect($success, $response);
    }

    /**
     * @return array|mixed
     */
    protected function buildRequest()
    {
        $result = [];
        $result = array_merge_recursive($result, $this->saleBuilder->build($this->quote));
        $result = array_merge_recursive($result, $this->accountBuilder->build($this->quote));
        $result = array_merge_recursive($result, $this->customerDataBuilder->build($this->quote));

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
        return $client->directDebitSale($requestData);
    }
}
