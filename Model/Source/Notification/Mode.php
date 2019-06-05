<?php

declare(strict_types=1);

/**
 * File: Mode.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Source\Notification;

use LizardMedia\PayLane\Model\Notification\Data;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class Mode
 * @package LizardMedia\PayLane\Model\Source\Notification
 */
class Mode implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => Data::MODE_MANUAL, 'label' => __('Manual')],
            ['value' => Data::MODE_AUTO, 'label' => __('Automatic')]
        ];
    }
}
