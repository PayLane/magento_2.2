<?php

declare(strict_types=1);

/**
 * File: Handle3ds.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Controller\Creditcard;

use Exception;
use LizardMedia\PayLane\Api\Config\GeneralConfigProviderInterface;
use LizardMedia\PayLane\Model\Notification\Data;
use LizardMedia\PayLane\Model\Payment\Adapter\Creditcard;
use LizardMedia\PayLane\Model\TransactionHandler;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Sales\Model\Order;

/**
 * Class Handle3ds
 * @package LizardMedia\PayLane\Controller\Creditcard
 */
class Handle3ds extends Action
{
    /**
     * @var GeneralConfigProviderInterface
     */
    private $generalConfigProvider;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var Creditcard
     */
    private $adapter;

    /**
     * @var TransactionHandler
     */
    private $transactionHandler;

    /**
     * @var CartManagementInterface
     */
    private $cartManagementInterface;

    /**
     * Handle3ds constructor.
     *
     * @param GeneralConfigProviderInterface $generalConfigProvider
     * @param Context $context
     * @param Order $order
     * @param TransactionHandler $transactionHandler
     * @param CartManagementInterface $cartManagementInterface
     * @param Creditcard $adapter
     */
    public function __construct(
        GeneralConfigProviderInterface $generalConfigProvider,
        Context $context,
        Order $order,
        TransactionHandler $transactionHandler,
        CartManagementInterface $cartManagementInterface,
        Creditcard $adapter
    ) {
        parent::__construct($context);
        $this->generalConfigProvider = $generalConfigProvider;
        $this->order = $order;
        $this->transactionHandler = $transactionHandler;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->adapter = $adapter;
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws Exception
     * @throws CouldNotSaveException
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $success = false;

        $status = $params['status'];
        $amount = $params['amount'];
        $currency = $params['currency'];
        $incrementId = $params['description'];
        $hash = $params['hash'];
        $idSecureAuth = isset($params['id_3dsecure_auth']) ? $params['id_3dsecure_auth'] : null;
        $hashSalt = $this->generalConfigProvider->getHashSalt();

        $hashComputed = sha1($hashSalt . '|' .
            $status . '|' .
            $incrementId . '|' .
            $amount . '|' .
            $currency . '|' .
            $idSecureAuth);

        $orderStatus = $this->generalConfigProvider->getErrorOrderStatus();

        if ($hash === $hashComputed && $status !== Data::STATUS_ERROR) {
            $orderStatus = $this->getOrderStatus($status);
        }

        $orderId = $this->cartManagementInterface->placeOrder($params['quote']);
        $order = $this->order->load($orderId);

        if ($order->getId()) {
            if ($status != Data::STATUS_ERROR) {
                $success = true;
                $comment = __(
                    'Payment handled via PayLane module | Card Authorized, Authorization ID: %1',
                    $idSecureAuth
                );

                $orderPayment = $order->getPayment();
                $orderPayment->setTransactionId($params['id_3dsecure_auth']);
                $orderPayment->setIsTransactionClosed(false);
                $orderPayment->addTransaction('authorization');
            } else {
                $error = $params['error'];
                $comment = __(
                    'Payment handled via PayLane module | Error (%1): %2',
                    $error['error_number'],
                    $error['error_description']
                );
            }

            $this->transactionHandler->setOrderState($order, $orderStatus, $comment);
            $order->save();
        }

        if ($success) {
            $this->adapter->handle3dsSale($idSecureAuth, $orderId, $this->getResponse());
        } else {
            $this->_redirect('checkout/onepage/failure', ['_nosid' => true, '_secure' => true]);
        }
    }

    /**
     * @param $notificationStatus
     * @return mixed
     */
    private function getOrderStatus($notificationStatus)
    {
        switch ($notificationStatus) {
            case Data::STATUS_PENDING:
                return $this->generalConfigProvider->getPendingOrderStatus();
            case Data::STATUS_PERFORMED:
                return $this->generalConfigProvider->getPerformedOrderStatus();
            case Data::STATUS_CLEARED:
                return $this->generalConfigProvider->getClearedOrderStatus();
            case Data::STATUS_ERROR:
            default:
                return $this->generalConfigProvider->getErrorOrderStatus();
        }
    }
}
