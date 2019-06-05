<?php

declare(strict_types=1);

/**
 * File: Status.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Source\Order;

use Magento\Framework\Option\ArrayInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Config;

/**
 * Class Status
 * @package LizardMedia\PayLane\Model\Source\Order
 */
class Status implements ArrayInterface
{
    // set null to enable all possible
    /**
     * @var array
     */
    protected $stateStatuses = [
        Order::STATE_NEW,
        Order::STATE_PENDING_PAYMENT,
        Order::STATE_PROCESSING,
        Order::STATE_COMPLETE,
        Order::STATE_CLOSED,
        Order::STATE_CANCELED,
        Order::STATE_HOLDED,
    ];

    /**
     * @var Config
     */
    protected $orderConfig;

    /**
     * Status constructor.
     * @param Config $orderConfig
     */
    public function __construct(Config $orderConfig)
    {
        $this->orderConfig = $orderConfig;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $statuses = $this->stateStatuses
            ? $this->orderConfig->getStateStatuses($this->stateStatuses)
            : $this->orderConfig->getStatuses();

        $options = [['value' => '', 'label' => __('-- Please Select --')]];
        foreach ($statuses as $code => $label) {
            $options[] = ['value' => $code, 'label' => $label];
        }
        return $options;
    }
}
