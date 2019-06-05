<?php

declare(strict_types=1);

/**
 * File: Handle.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Cron\Notification;

use LizardMedia\PayLane\Api\Adapter\PayLaneRestClientFactoryInterface;
use LizardMedia\PayLane\Api\Config\GeneralConfigProviderInterface;
use LizardMedia\PayLane\Api\Config\NotificationConfigProviderInterface;
use LizardMedia\PayLane\Model\Notification\Data;
use LizardMedia\PayLane\Model\TransactionHandler;
use LizardMedia\PayLane\Model\Ui\ConfigProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Handle
 * @package LizardMedia\PayLane\Cron\Notification
 */
class Handle
{
    /**
     * @var GeneralConfigProviderInterface
     */
    private $generalConfigProvider;

    /**
     * @var NotificationConfigProviderInterface
     */
    private $notificationConfigProvider;

    /**
     * @var PayLaneRestClientFactoryInterface
     */
    private $payLaneRestClientFactory;

    /**
     * @var TransactionHandler
     */
    private $transactionHandler;

    /**
     * @var CollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Handle constructor.
     * @param GeneralConfigProviderInterface $generalConfigProvider
     * @param NotificationConfigProviderInterface $notificationConfigProvider
     * @param PayLaneRestClientFactoryInterface $payLaneRestClientFactory
     * @param TransactionHandler $transactionHandler
     * @param CollectionFactory $orderCollectionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        GeneralConfigProviderInterface $generalConfigProvider,
        NotificationConfigProviderInterface $notificationConfigProvider,
        PayLaneRestClientFactoryInterface $payLaneRestClientFactory,
        TransactionHandler $transactionHandler,
        CollectionFactory $orderCollectionFactory,
        LoggerInterface $logger
    ) {
        $this->generalConfigProvider = $generalConfigProvider;
        $this->notificationConfigProvider = $notificationConfigProvider;
        $this->transactionHandler = $transactionHandler;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->logger = $logger;
        $this->payLaneRestClientFactory = $payLaneRestClientFactory;
    }

    /**
     * @return $this
     * @throws LocalizedException
     * @throws \Exception
     */
    public function execute()
    {
        if ($this->isManualMode()) {
            $orders = $this->prepareOrderCollection();
            $client = $this->payLaneRestClientFactory->create();
            $performedOrderStatus = $this->generalConfigProvider->getPerformedOrderStatus();
            $clearedOrderStatus = $this->generalConfigProvider->getClearedOrderStatus();

            foreach ($orders as $order) {
                $saleId = $this->getPaylaneSaleId($order);
                $this->log(__('Handle order #%1, Paylane Sale ID %2', $order->getIncrementId(), $saleId)->getText());

                if (!empty($saleId)) {
                    $result = $client->getSaleInfo($saleId);

                    if ($result['success'] && $saleId == $result['id_sale']) {
                        if ($result['status'] == Data::STATUS_PERFORMED
                            && $order->getStatus() != $performedOrderStatus) {
                            $this->handleOrderStateChange($order, $performedOrderStatus, Data::STATUS_PERFORMED);
                        } elseif ($result['status'] == Data::STATUS_CLEARED
                            && $order->getStatus() != $clearedOrderStatus
                        ) {
                            $this->handleOrderStateChange($order, $clearedOrderStatus, Data::STATUS_CLEARED);
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @TODO: Limit attributes loaded
     * @return Collection
     */
    protected function prepareOrderCollection()
    {
        $paymentCodes = $this->generalConfigProvider->getPaymentCodes();
        $collection = $this->orderCollectionFactory->create()->addAttributeToSelect('*');

        $collection->join(
            ['payment' => 'sales/order_payment'],
            'main_table.entity_id = payment.parent_id',
            ['payment.method' => 'payment.method']
        );

        $collection->addFieldToFilter('payment.method', ['in' => $paymentCodes])
            ->setOrder('increment_id', Collection::SORT_ORDER_DESC);

        return $collection;
    }

    /**
     * @param Order $order
     * @return mixed
     */
    protected function getPaylaneSaleId(Order $order)
    {
        $payment = $order->getPayment();
        return $payment->getTransactionId();
    }

    /**
     * @return bool
     */
    protected function isManualMode(): bool
    {
        return $this->notificationConfigProvider->getNotificationHandlingMode() === Data::MODE_MANUAL;
    }

    /**
     * @param string $message
     * @return void
     */
    protected function log(string $message)
    {
        if ($this->notificationConfigProvider->isLoggingEnabled()) {
            $this->logger->info($message);
        }
    }

    /**
     * @param Order $order
     * @param string $status
     * @param string $paylaneMappedStatus
     * @return void
     * @throws LocalizedException
     * @throws \Exception
     */
    protected function handleOrderStateChange(Order $order, string $status, string $paylaneMappedStatus)
    {
        $comment = __('Order status changed via PayLane module');
        $this->transactionHandler->setOrderState($order, $status, $comment);
        $order->save();

        $this->log(__(
            'Changed order status to: %1, %2 in PayLane',
            $status,
            $paylaneMappedStatus
        )->getText());
    }
}
