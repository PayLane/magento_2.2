<?php

declare(strict_types=1);

/**
 * File: Version.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Source\Redirect;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Version
 * @package LizardMedia\PayLane\Model\Source\Redirect
 */
class Version implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'GET', 'label' => __('GET')],
            ['value' => 'POST', 'label' => __('POST')],
        ];
    }
}
