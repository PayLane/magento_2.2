<?php

declare(strict_types=1);

/**
 * File: Handle.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Controller\SecureForm;

use Exception;
use LizardMedia\PayLane\Api\Config\GeneralConfigProviderInterface;
use LizardMedia\PayLane\Model\Notification\Data;
use LizardMedia\PayLane\Model\TransactionHandler;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Sales\Model\Order;

/**
 * Class Handle
 * @package LizardMedia\PayLane\Controller\SecureForm
 */
class Handle extends Action
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
     * @var TransactionHandler
     */
    private $transactionHandler;

    /**
     * @var CartManagementInterface
     */
    private $cartManagementInterface;

    /**
     * Handle constructor.
     * @param GeneralConfigProviderInterface $generalConfigProvider
     * @param Context $context
     * @param Order $order
     * @param TransactionHandler $transactionHandler
     * @param CartManagementInterface $cartManagementInterface
     */
    public function __construct(
        GeneralConfigProviderInterface $generalConfigProvider,
        Context $context,
        Order $order,
        TransactionHandler $transactionHandler,
        CartManagementInterface $cartManagementInterface
    ) {
        parent::__construct($context);
        $this->generalConfigProvider = $generalConfigProvider;
        $this->order = $order;
        $this->transactionHandler = $transactionHandler;
        $this->cartManagementInterface = $cartManagementInterface;
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
        $idSale = isset($params['id_sale']) ? $params['id_sale'] : null;
        $hashSalt = $this->generalConfigProvider->getHashSalt();

        $hashComputed = sha1($hashSalt . '|' .
            $status . '|' .
            $incrementId . '|' .
            $amount . '|' .
            $currency . '|' .
            $idSale);

        $orderStatus = $this->generalConfigProvider->getErrorOrderStatus();

        if ($hash === $hashComputed && $status !== Data::STATUS_ERROR) {
            $orderStatus = $this->getOrderStatus($status);
        }

        $orderId = $this->cartManagementInterface->placeOrder($params['quote']);
        $order = $this->order->load($orderId);

        if ($order->getId()) {
            if ($status != Data::STATUS_ERROR) {
                $success = true;
                $comment = __('Payment handled via PayLane module | Transaction ID: %1', $idSale);
                $orderPayment = $order->getPayment();
                $orderPayment->setTransactionId($idSale);

                if ($status == Data::STATUS_PENDING) {
                    $orderPayment->setIsTransactionClosed(false);
                    $orderPayment->addTransaction('authorization');
                } elseif (in_array($status, [Data::STATUS_PERFORMED, Data::STATUS_CLEARED])) {
                    $orderPayment->setIsTransactionClosed(true);
                    $orderPayment->addTransaction('capture');
                }
            } else {
                $comment = __('Payment handled via PayLane module | Error: %1', $params['error_text']);
            }
        
            $this->transactionHandler->setOrderState($order, $orderStatus, $comment);
            $order->save();
        }

        if ($success) {
            $this->_redirect('checkout/onepage/success', ['_nosid' => true, '_secure' => true]);
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
        $orderStatus = $this->generalConfigProvider->getErrorOrderStatus();

        if ($notificationStatus === Data::STATUS_PENDING) {
            $orderStatus = $this->generalConfigProvider->getPendingOrderStatus();
        }

        if ($notificationStatus === Data::STATUS_PERFORMED) {
            $orderStatus = $this->generalConfigProvider->getPerformedOrderStatus();
        }

        if ($notificationStatus === Data::STATUS_CLEARED) {
            $orderStatus = $this->generalConfigProvider->getClearedOrderStatus();
        }

        if ($notificationStatus === Data::STATUS_ERROR) {
            $orderStatus = $this->generalConfigProvider->getErrorOrderStatus();
        }

        return $orderStatus;
    }
}
