<?php

declare(strict_types=1);

/**
 * File: Creditcard.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Payment\Adapter;

use Exception;
use LizardMedia\PayLane\Api\Adapter\PayLaneRestClientFactoryInterface;
use LizardMedia\PayLane\Api\Config\GeneralConfigProviderInterface;
use LizardMedia\PayLane\Api\Config\Methods\CreditCardConfigProviderInterface;
use LizardMedia\PayLane\Model\Payment\Request\Builder\CustomerDataBuilder;
use LizardMedia\PayLane\Model\Payment\Request\Builder\Ds3SecureBuilder;
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
 * Class Creditcard
 * @package LizardMedia\PayLane\Model\Payment\Adapter
 */
class Creditcard extends AbstractAdapter
{
    /**
     * @var CreditCardConfigProviderInterface
     */
    private $creditCardConfigProvider;

    /**
     * @var GeneralConfigProviderInterface
     */
    protected $generalConfigProvider;

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
    protected $customerBuilder;

    /**
     * @var Ds3SecureBuilder
     */
    protected $ds3Builder;

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
     * Creditcard constructor.
     * @param CreditCardConfigProviderInterface $creditCardConfigProvider
     * @param GeneralConfigProviderInterface $generalConfigProvider
     * @param PayLaneRestClientFactoryInterface $payLaneRestClientFactory
     * @param SaleDataBuilder $saleBuilder
     * @param TokenBuilder $tokenBuilder
     * @param CustomerDataBuilder $customerBuilder
     * @param RedirectInterface $redirect
     * @param Order $order
     * @param CartManagementInterface $cartManagementInterface
     * @param TransactionHandler $transactionHandler
     * @param Ds3SecureBuilder $ds3Builder
     */
    public function __construct(
        CreditCardConfigProviderInterface $creditCardConfigProvider,
        GeneralConfigProviderInterface $generalConfigProvider,
        PayLaneRestClientFactoryInterface $payLaneRestClientFactory,
        SaleDataBuilder $saleBuilder,
        TokenBuilder $tokenBuilder,
        CustomerDataBuilder $customerBuilder,
        RedirectInterface $redirect,
        Order $order,
        CartManagementInterface $cartManagementInterface,
        TransactionHandler $transactionHandler,
        Ds3SecureBuilder $ds3Builder
    ) {
        parent::__construct($payLaneRestClientFactory,$redirect);
        $this->creditCardConfigProvider = $creditCardConfigProvider;
        $this->generalConfigProvider = $generalConfigProvider;
        $this->saleBuilder = $saleBuilder;
        $this->tokenBuilder = $tokenBuilder;
        $this->customerBuilder = $customerBuilder;
        $this->ds3Builder = $ds3Builder;
        $this->redirect = $redirect;
        $this->order = $order;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->transactionHandler = $transactionHandler;
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
        if ($this->is3dsEnabled()) {
            $this->handle3dSecureEnrollment($responseData, $response);
        } else {
            $this->handleSale($responseData, $response);
        }
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
     * @param $idAuth
     * @param $orderId
     * @param ResponseInterface $response
     * @return void
     * @throws Exception
     * @throws LocalizedException
     */
    public function handle3dsSale($idAuth, $orderId, ResponseInterface $response)
    {
        $client = $this->payLaneRestClientFactory->create();

        $responseData = $client->saleBy3DSecureAuthorization(['id_3dsecure_auth' => $idAuth]);

        $this->handle3dSecureSale($responseData, $orderId, $response);
    }

    /**
     * @return array|mixed
     */
    protected function buildRequest()
    {
        $result = [];
        $result = array_merge_recursive($result, $this->saleBuilder->build($this->quote));
        $result = array_merge_recursive($result, $this->tokenBuilder->build($this->quote));
        $result = array_merge_recursive($result, $this->customerBuilder->build($this->quote));

        if ($this->is3dsEnabled()) {
            $result = array_merge_recursive($result, $this->ds3Builder->build($this->quote));
        }

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

        if ($this->is3dsEnabled()) {
            return $client->checkCard3DSecureByToken($requestData);
        } else {
            return $client->cardSaleByToken($requestData);
        }
    }

    /**
     * @param array $responseData
     * @param $orderId
     * @param ResponseInterface $response
     * @return void
     * @throws Exception
     * @throws LocalizedException
     */
    protected function handle3dSecureSale(array $responseData, $orderId, ResponseInterface $response)
    {
        $success = false;
        $orderStatus = $this->generalConfigProvider->getErrorOrderStatus();
        $order = $this->order->load($orderId);

        if ($responseData['success']) {
            $orderStatus = $this->generalConfigProvider->getClearedOrderStatus();
            $comment = __('Payment handled via PayLane module | Transaction ID: %1', $responseData['id_sale']);
            $orderPayment = $order->getPayment();
            $orderPayment->setTransactionId($responseData['id_sale']);
            $orderPayment->setIsTransactionClosed(true);
            $orderPayment->addTransaction('capture');
            $success = true;
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

        $this->handleRedirect($success, $response);
    }

    /**
     * @param array $responseData
     * @param ResponseInterface $response
     * @return void
     * @throws Exception
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    protected function handle3dSecureEnrollment(array $responseData, ResponseInterface $response)
    {
        $success = false;
        $orderStatus = $this->generalConfigProvider->getErrorOrderStatus();

        if (!empty($responseData['is_card_enrolled']) && $responseData['is_card_enrolled']) {
            header('Location: ' . $responseData['redirect_url']);
            die;
        } else {
            $quote = $this->quote;
            $orderId = $this->cartManagementInterface->placeOrder($quote->getId());
            $order = $this->order->load($orderId);
            $comment = __('Payment handled via PayLane module | Error: Card not enrolled to 3-D Secure program');
            $this->transactionHandler->setOrderState($order, $orderStatus, $comment);
            $order->save();
        }

        $this->handleRedirect($success, $response);
    }

    /**
     * @param array $responseData
     * @param ResponseInterface $response
     * @return void
     * @throws Exception
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    protected function handleSale(array $responseData, ResponseInterface $response)
    {
        $success = false;
        $quote = $this->quote;
        $orderId = $this->cartManagementInterface->placeOrder($quote->getId());
        $order = $this->order->load($orderId);
        $orderStatus = $this->generalConfigProvider->getErrorOrderStatus();

        if ($order->getId()) {
            if ($responseData['success']) {
                $orderStatus = $this->generalConfigProvider->getClearedOrderStatus();
                $success = true;
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

        $this->handleRedirect($success, $response);
    }

    /**
     * @return mixed
     */
    protected function is3dsEnabled()
    {
        return $this->creditCardConfigProvider->is3DSCheckEnabled();
    }
}
