<?php

declare(strict_types=1);

/**
 * File:TransactionHandler.php
 *
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model;

use Exception;
use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Convert\Order as ConvertOrder;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Status\Collection;

/**
 * Class TransactionHandler
 * @package LizardMedia\PayLane\Model
 */
class TransactionHandler
{
    /**
     * @var string
     */
    const TYPE_SALE = 'S';

    /**
     * @var string
     */
    const TYPE_AUTHORIZATION = 'A';

    /**
     * @var string
     */
    const TYPE_REFUND = 'R';

    /**
     * @var string
     */
    const TYPE_CANCEL = 'C';

    /**
     * @var TransactionFactory
     */
    private $transactionFactory;

    /**
     * @var Collection
     */
    private $statusCollection;

    /**
     * @var ConvertOrder
     */
    private $convertOrder;

    /**
     * TransactionHandler constructor.
     * @param TransactionFactory $transactionFactory
     * @param Collection $collection
     * @param ConvertOrder $convertOrder
     */
    public function __construct(
        TransactionFactory $transactionFactory,
        Collection $collection,
        ConvertOrder $convertOrder
    ) {
        $this->transactionFactory = $transactionFactory;
        $this->statusCollection = $collection;
        $this->convertOrder = $convertOrder;
    }

    /**
     * Set order state with ability to create
     * invoice and shipment when state is COMPLETED
     *
     * @param Order $order
     * @param string $status
     * @param string|null $comment
     * @param bool $customerNotify
     * @return Order
     * @throws Exception
     * @throws LocalizedException
     */
    public function setOrderState(Order $order, $status, $comment = null, $customerNotify = false)
    {
        if ($status === Order::STATE_COMPLETE) {
            /**
             * To set order in "complete" state, there must be
             * created invoice and shipment (if product is not virtual)
             */
            $invoice = $order->prepareInvoice()
                ->setTransactionId($order->getId())
                ->addComment("Invoice created by PayLane payment module.")
                ->register()
                ->pay();

            $this->transactionFactory->create()
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();

            if ($order->canShip()) {
                $shipment = $this->convertOrder->toShipment($order);

                foreach ($order->getAllItems() as $orderItem) {
                    if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                        continue;
                    }

                    $qtyShipped = $orderItem->getQtyToShip();
                    $shipmentItem = $this->convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                    $shipment->addItem($shipmentItem);
                }

                if ($shipment) {
                    $shipment->register();
                    $shipment->getOrder()->setIsInProcess(true);
                    $this->transactionFactory->create()
                        ->addObject($shipment)
                        ->addObject($shipment->getOrder())
                        ->save();
                }
            }
        }

        $order->setState($this->getStateByStatus($status));
        $order->setStatus($status);
        $history = $order->addStatusHistoryComment($comment);
        $history->setIsCustomerNotified($customerNotify);
        $history->save();
        $order->save();

        return $order;
    }

    /**
     * @param string $status
     * @return mixed
     */
    public function getStateByStatus($status)
    {
        $model = $this->statusCollection
            ->addFieldToFilter('main_table.status', $status)
            ->getFirstItem();

        return $model['state'];
    }
}
