<?php

declare(strict_types=1);

/**
 * File: Mode.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Source\Payment;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Mode
 * @package LizardMedia\PayLane\Model\Source\Payment
 */
class Mode implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'API', 'label' => __('API')],
            ['value' => 'SECURE_FORM', 'label' => __('Secure Form')],
        ];
    }
}
