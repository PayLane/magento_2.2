<?php

declare(strict_types=1);

/**
 * File: Level.php
 *
 * @copyright Copyright (C) 2019 Lizard Media (http://lizardmedia.pl)
 */

namespace LizardMedia\PayLane\Model\Source\Avs\Check;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Level
 * @package LizardMedia\PayLane\Model\Source\Avs\Check
 */
class Level implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => '0'],
            ['value' => 1, 'label' => '1'],
            ['value' => 2, 'label' => '2'],
            ['value' => 3, 'label' => '3'],
            ['value' => 4, 'label' => '4'],
        ];
    }
}
